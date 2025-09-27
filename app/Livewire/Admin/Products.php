<?php
namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MongoProduct;

class Products extends Component
{
    use WithPagination;

    public string $q = '';

    protected $queryString = ['q'];

    public function updatingQ()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = MongoProduct::query();

        if ($this->q !== '') {
            $query->where('name', 'like', '%' . $this->q . '%')
                  ->orWhere('category', 'like', '%' . $this->q . '%')
                  ->orWhere('description', 'like', '%' . $this->q . '%');
        }

        return view('livewire.admin.products', [
            'products' => $query->orderBy('name')->paginate(10),
        ]);
    }
}
