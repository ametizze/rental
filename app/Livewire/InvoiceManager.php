<?php
// app/Livewire/InvoiceManager.php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\StockItem;
use Illuminate\Support\Facades\DB;
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

    public function markAsPaid($invoiceId)
    {
        $invoice = Invoice::findOrFail($invoiceId);

        if ($invoice->status === 'paid') {
            session()->flash('info', __('Invoice is already paid.'));
            return;
        }

        // Seta paid_amount para o total para liquidar a fatura
        $invoice->update([
            'paid_amount' => $invoice->total,
            'status' => 'paid',
        ]);

        session()->flash('success', __('Invoice successfully marked as paid.'));
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

    /**
     * Exclui a fatura e executa a lógica de estorno de estoque, se aplicável.
     * * @param int $invoiceId
     * @return void
     */
    public function deleteInvoice($invoiceId)
    {
        // Inicia a transação para garantir que o estoque só seja estornado se a fatura for deletada
        DB::beginTransaction();
        try {
            $invoice = Invoice::findOrFail($invoiceId);

            // 1. CHECAGEM CRÍTICA: Bloqueia a exclusão se houver pagamentos.
            // O valor deve ser zero, pois pagamentos parciais são rastreados.
            if ($invoice->paid_amount > 0) {
                session()->flash('error', __('Cannot delete invoice: It has payments recorded. Please refund/delete payments first.'));
                DB::rollBack();
                return;
            }

            // 2. ESTORNO DE ESTOQUE (Se for uma venda rápida)
            // Filtra os itens da fatura que têm um link direto para um StockItem.
            $stockItemsToRestore = $invoice->items->filter(fn($item) => $item->stock_item_id !== null);

            if ($stockItemsToRestore->isNotEmpty()) {
                foreach ($stockItemsToRestore as $item) {
                    // Usa o ID da chave estrangeira para incrementar (devolver) o estoque.
                    StockItem::where('id', $item->stock_item_id)->increment('quantity', $item->quantity);
                }
            }

            // 3. EXCLUSÃO FINAL E COMMIT
            // A exclusão da Invoice deve, por sua vez, acionar a exclusão em cascata dos InvoiceItems.
            $invoice->delete();

            DB::commit();
            session()->flash('success', __('Invoice deleted successfully and stock (if applicable) has been restored.'));
        } catch (\Exception $e) {
            DB::rollBack();
            // Em um ambiente de produção, registre o erro completo para debug.
            \Log::error("Failed to delete invoice {$invoiceId}: " . $e->getMessage());
            session()->flash('error', __('An error occurred while deleting the invoice.'));
        }
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
