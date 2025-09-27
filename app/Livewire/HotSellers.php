<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\MongoProduct;

class HotSellers extends Component
{
    public $products = [];

    public function mount()
    {
        $this->products = MongoProduct::orderByDesc('sold_pieces')
            ->take(5)
            ->get()
            ->map(fn ($p) => [
                'id'     => (string) $p->_id,
                'name'   => $p->name,
                'price'  => $p->price,
                'image'  => $p->image
                    ? (str_starts_with($p->image, 'data:image')
                        ? $p->image
                        : 'data:image/png;base64,' . $p->image)
                    : 'https://placehold.co/400x400/png',
                'status' => $p->status ?? 'Instock',
            ]);
    }

    public function render()
    {
        return view('livewire.hot-sellers');
    }
}
