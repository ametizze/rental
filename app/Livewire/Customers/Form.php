<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Component;

class Form extends Component
{
    #[Locked]
    public ?int $customerId = null;

    public string $code = '';
    public string $name = '';
    public ?string $email = null;
    public ?string $phone = null;
    public ?string $alt_phone = null;
    public ?string $notes = null;

    public function mount(?Customer $customer = null)
    {
        if ($customer && $customer->exists) {
            $this->customerId = $customer->id;
            $this->code = $customer->code;
            $this->name = $customer->name;
            $this->email = $customer->email;
            $this->phone = $customer->phone;
            $this->alt_phone = $customer->alt_phone;
            $this->notes = $customer->notes;
        }
    }

    public function rules()
    {
        $tid = tenant_id();
        return [
            'code' => [
                'required',
                'max:30',
                \Illuminate\Validation\Rule::unique('customers')
                    ->where(fn($q) => $q->where('tenant_id', $tid))
                    ->ignore($this->customerId),
            ],
            'name' => ['required', 'max:180'],
            'email' => ['nullable', 'email', 'max:150'],
            'phone' => ['nullable', 'max:50'],
            'alt_phone' => ['nullable', 'max:50'],
            'notes' => ['nullable'],
        ];
    }

    public function save()
    {
        $tid = tenant_id();
        if (!$tid) {
            $this->addError('tenant', 'Selecione um tenant para salvar.');
            return;
        }

        $data = $this->validate();
        $data['tenant_id'] = $tid;

        $customer = \App\Models\Customer::updateOrCreate(
            ['id' => $this->customerId],
            $data
        );

        session()->flash('success', 'Cliente salvo com sucesso.');
        return redirect()->route('customers.show', $customer);
    }

    public function render()
    {
        return view('livewire.customers.form');
    }
}
