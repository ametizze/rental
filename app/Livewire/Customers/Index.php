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
        // $tenantId = session('tenant_id', 1);
        $tenantId = request()->attributes->get('tenant_id')
            ?? session('tenant_id')
            ?? optional(auth()->user())->tenant_id
            ?? 1;

        $customers = Customer::query()
            ->where('tenant_id', $tenantId)
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
