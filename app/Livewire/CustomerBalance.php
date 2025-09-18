<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\Invoice;
use Livewire\Component;

class CustomerBalance extends Component
{
    public $customerId;
    public $customers;
    public $unpaidInvoices = [];
    public $totalBalance = 0;

    public function mount()
    {
        $this->customers = Customer::all();
    }

    public function updatedCustomerId()
    {
        if ($this->customerId) {
            $this->unpaidInvoices = Invoice::where('customer_id', $this->customerId)
                ->where('status', 'unpaid')
                ->get();
            $this->totalBalance = $this->unpaidInvoices->sum('total');
        } else {
            $this->unpaidInvoices = [];
            $this->totalBalance = 0;
        }
    }

    public function render()
    {
        return view('livewire.customer-balance');
    }
}
