<?php
// App\Livewire\Products.php

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
    public $products;

    public function mount()
    {
        $this->loadCategories();
        $this->apply();
    }

    // Add this
    public function refreshData(): void
    {
        $this->apply();          // re-run filters & refresh products
        $this->loadCategories(); // pick up any new categories added by admin
    }

    private function loadCategories(): void
    {
        try {
            $this->categories = DB::connection('mongodb')
                ->getMongoDB()->selectCollection('products')
                ->distinct('category') ?? [];
            sort($this->categories);
        } catch (\Throwable $e) {
            // ignore if Mongo not reachable, keep current categories
        }
    }

    public function apply(): void
    {
        $q = MongoProduct::query();

        if ($this->category !== '') $q->where('category', $this->category);
        if (is_numeric($this->min_price)) $q->where('price', '>=', (float)$this->min_price);
        if (is_numeric($this->max_price)) $q->where('price', '<=', (float)$this->max_price);

        $norm = collect($this->availability)->map(fn($v) => strtolower(trim($v)))->all();
        $statuses = [];
        if (in_array('instock', $norm, true)) $statuses[] = 'Instock';
        if (in_array('out of stock', $norm, true)) $statuses[] = 'Out of Stock';
        if ($statuses) $q->whereIn('status', $statuses);

        $this->products = $q->get();
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
