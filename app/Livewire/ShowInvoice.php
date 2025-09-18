<?php

namespace App\Livewire;

use App\Models\Invoice;
use Livewire\Component;

class ShowInvoice extends Component
{
    public Invoice $invoice;

    public function mount(Invoice $invoice)
    {
        // Carrega todas as relações necessárias de forma explícita
        $this->invoice = $invoice->load(['customer', 'items']);
    }

    public function markAsPaid()
    {
        if ($this->invoice->status === 'paid') {
            session()->flash('info', __('Invoice is already paid.'));
            return;
        }

        $this->invoice->update(['status' => 'paid']);
        $this->dispatch('invoiceStatusChanged');
        session()->flash('success', __('Invoice marked as paid successfully.'));
    }

    public function render()
    {
        return view('livewire.show-invoice');
    }
}
