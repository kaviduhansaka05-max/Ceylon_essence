<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User; // or your customer model

class Customers extends Component
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
        $query = User::query();

        if ($this->q !== '') {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->q . '%')
                  ->orWhere('email', 'like', '%' . $this->q . '%');
            });
        }

        return view('livewire.admin.customers', [
            'users' => $query->orderByDesc('created_at')->paginate(10),
        ]);
    }
}
