<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use Livewire\Component;

class Show extends Component
{
    public Customer $customer;

    public function mount()
    {
        if (tenant_id() && $this->customer->tenant_id !== tenant_id()) {
            abort(404);
        }
    }

    public function render()
    {
        return view('livewire.customers.show');
    }
}
