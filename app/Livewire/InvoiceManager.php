<?php
// app/Livewire/InvoiceManager.php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\Invoice;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

class InvoiceManager extends Component
{
    use WithFileUploads;

    public $invoiceId;
    public $customer_id, $due_date, $notes, $tax_rate;
    public $photos = [];
    public $invoiceItems = [];
    public $subtotal = 0, $tax_amount = 0, $total = 0;

    protected $rules = [
        'customer_id' => 'required|exists:customers,id',
        'due_date' => 'required|date',
        'invoiceItems' => 'required|array|min:1',
        'invoiceItems.*.description' => 'required|string|max:255',
        'invoiceItems.*.quantity' => 'required|numeric|min:0.01',
        'invoiceItems.*.rate' => 'required|numeric|min:0',
        'tax_rate' => 'nullable|numeric|min:0|max:1',
    ];

    public function mount()
    {
        $this->tax_rate = optional(auth()->user()->tenant)->settings['tax_rate'] ?? 0;

        $this->addItem();
    }

    public function updated($propertyName)
    {
        // Realiza os cálculos dinamicamente a cada alteração
        if (Str::startsWith($propertyName, 'invoiceItems') || $propertyName === 'tax_rate') {
            $this->calculateTotals();
        }
    }

    public function calculateTotals()
    {
        $this->subtotal = 0;
        foreach ($this->invoiceItems as $item) {
            $this->subtotal += (float)$item['quantity'] * (float)$item['rate'];
        }
        $this->tax_amount = $this->subtotal * (float)$this->tax_rate;
        $this->total = $this->subtotal + $this->tax_amount;
    }

    public function addItem()
    {
        $this->invoiceItems[] = [
            'description' => '',
            'quantity' => 1,
            'rate' => 0,
            'amount' => 0,
        ];
    }

    public function removeItem($index)
    {
        unset($this->invoiceItems[$index]);
        $this->invoiceItems = array_values($this->invoiceItems);
        $this->calculateTotals();
    }

    public function save()
    {
        $this->validate();

        $customer = Customer::find($this->customer_id);

        $invoice = Invoice::create([
            'uuid' => (string) Str::uuid(),
            'customer_id' => $this->customer_id,
            'bill_to_name' => $customer->name,
            'bill_to_email' => $customer->email,
            'bill_to_phone' => $customer->phone,
            'tax_rate' => $this->tax_rate,
            'subtotal' => $this->subtotal,
            'tax_amount' => $this->tax_amount,
            'total' => $this->total,
            'due_date' => $this->due_date,
            'notes' => $this->notes,
            // 'photos' => $this->photos ? json_encode($this->photos->storeMultiple('invoice_photos', 'public')) : null,
        ]);

        foreach ($this->invoiceItems as $item) {
            $invoice->items()->create([
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'rate' => $item['rate'],
                'amount' => $item['quantity'] * $item['rate'],
            ]);
        }

        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset(['customer_id', 'due_date', 'notes', 'photos', 'invoiceItems', 'subtotal', 'tax_amount', 'total']);
        $this->addItem();
    }

    public function render()
    {
        $customers = Customer::all();
        $invoices = Invoice::with('customer')->latest()->paginate(20);

        return view('livewire.invoice-manager', [
            'customers' => $customers,
            'invoices' => $invoices,
        ]);
    }
}
