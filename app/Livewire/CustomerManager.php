<?php
// app/Livewire/CustomerManager.php

namespace App\Livewire;

use App\Models\Customer;
use Livewire\Component;

class CustomerManager extends Component
{
    public $customerId;
    public $name, $email, $phone;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'nullable|email|max:255',
        'phone' => 'nullable|string|max:255',
    ];

    public function save()
    {
        $this->validate();

        Customer::updateOrCreate(['id' => $this->customerId], [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
        ]);

        $this->resetForm();
    }

    public function edit(Customer $customer)
    {
        $this->customerId = $customer->id;
        $this->name = $customer->name;
        $this->email = $customer->email;
        $this->phone = $customer->phone;
    }

    public function delete($id)
    {
        Customer::destroy($id);
    }

    public function resetForm()
    {
        $this->reset(['name', 'email', 'phone', 'customerId']);
    }

    public function render()
    {
        $customers = Customer::all();

        return view('livewire.customer-manager', [
            'customers' => $customers,
        ]);
    }
}
