<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MongoOrder;

class Orders extends Component
{
    use WithPagination;

    public string $status = '';
    protected $queryString = ['status'];

    public function updatingStatus() { $this->resetPage(); }

    public function render()
    {
        $q = MongoOrder::query();
        if ($this->status !== '') {
            $q->where('status', $this->status);
        }

        return view('livewire.admin.orders', [
            'orders' => $q->orderByDesc('created_at')->paginate(10),
        ]);
    }
}

