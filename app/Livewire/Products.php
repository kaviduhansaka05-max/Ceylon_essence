<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;
use App\Models\MongoProduct;

#[Layout('layouts.app')]
class Products extends Component
{
    public string $category = '';
    public $min_price = null;
    public $max_price = null;
    public array $availability = [];

    public array $categories = [];
    public $products = [];

    public function mount()
    {
        $this->loadCategories();
        $this->apply();
    }

    public function refreshData(): void
    {
        $this->apply();
        $this->loadCategories();
    }

    private function loadCategories(): void
    {
        try {
            $raw = DB::connection('mongodb')
                ->getMongoDB()
                ->selectCollection('products')
                ->distinct('category');

            $this->categories = array_filter(array_map('trim', $raw));
            sort($this->categories);
        } catch (\Throwable $e) {
            $this->categories = [];
        }
    }

    public function apply(): void
    {
        $q = MongoProduct::query();

        if ($this->category !== '') {
            $q->where('category', $this->category);
        }
        if (is_numeric($this->min_price)) {
            $q->where('price', '>=', (float) $this->min_price);
        }
        if (is_numeric($this->max_price)) {
            $q->where('price', '<=', (float) $this->max_price);
        }

        if (!empty($this->availability)) {
            $map = collect($this->availability)
                ->map(fn($v) => strtolower(trim($v)))
                ->all();

            $statuses = [];
            if (in_array('instock', $map, true)) {
                $statuses[] = 'Instock';
            }
            if (in_array('out of stock', $map, true)) {
                $statuses[] = 'Out of Stock';
            }
            if ($statuses) {
                $q->whereIn('status', $statuses);
            }
        }

        $this->products = $q->orderBy('created_at', 'desc')->get()->toArray();
    }

    public function resetFilters(): void
    {
        $this->category = '';
        $this->min_price = null;
        $this->max_price = null;
        $this->availability = [];
        $this->apply();
    }

    public function render()
    {
        return view('livewire.products', [
            'products'   => $this->products,
            'categories' => $this->categories,
        ]);
    }
}
