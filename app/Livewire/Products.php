<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\MongoProduct;
use Illuminate\Support\Facades\DB;

class Products extends Component
{
    // filters
    public string $category = '';
    public string $min_price = '';
    public string $max_price = '';
    /** @var array<int,string> */
    public array $availability = []; // ['Instock','Out of Stock']

    /** @var array<int,string> */
    public array $categories = [];

    public function mount(): void
    {
        // load distinct categories for the left select
        try {
            $this->categories = DB::connection('mongodb')
                ->getMongoDB()
                ->selectCollection('products')
                ->distinct('category') ?? [];
            sort($this->categories);
        } catch (\Throwable $e) {
            $this->categories = MongoProduct::query()
                ->pluck('category')->filter()->unique()->sort()->values()->all();
        }
    }

    // “Apply filters” button (no-op, just triggers re-render)
    public function apply(): void {}

    public function resetFilters(): void
    {
        $this->category = '';
        $this->min_price = '';
        $this->max_price = '';
        $this->availability = [];
    }

    private function buildQuery()
    {
        $q = MongoProduct::query();

        if ($this->category !== '') {
            $q->where('category', $this->category);
        }

        $min = is_numeric($this->min_price) ? (float) $this->min_price : null;
        $max = is_numeric($this->max_price) ? (float) $this->max_price : null;

        if ($min !== null && $max !== null && $min <= $max) {
            $q->whereBetween('price', [$min, $max]);
        } elseif ($min !== null) {
            $q->where('price', '>=', $min);
        } elseif ($max !== null) {
            $q->where('price', '<=', $max);
        }

        $norm = array_map(fn($v) => strtolower(trim($v)), (array) $this->availability);
        $statuses = [];
        if (in_array('instock', $norm, true))      $statuses[] = 'Instock';
        if (in_array('out of stock', $norm, true) ||
            in_array('out_of_stock', $norm, true) ||
            in_array('outofstock', $norm, true))    $statuses[] = 'Out of Stock';

        if ($statuses) $q->whereIn('status', $statuses);

        return $q->orderBy('name');
    }

    public function render()
    {
        return view('livewire.products', [
            'products' => $this->buildQuery()->get(),
        ]);
    }
}
