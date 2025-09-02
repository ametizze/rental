<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public string $q = '';

    public function updatingQ()
    {
        $this->resetPage();
    }

    public function render()
    {
        $tid = tenant_id();

        $customers = \App\Models\Customer::query()
            ->when($tid, fn($q) => $q->where('tenant_id', $tid))
            ->when(
                $this->q,
                fn($q) =>
                $q->where(function ($w) {
                    $w->where('name', 'like', "%{$this->q}%")
                        ->orWhere('code', 'like', "%{$this->q}%")
                        ->orWhere('email', 'like', "%{$this->q}%");
                })
            )
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.customers.index', compact('customers'));
    }
}
