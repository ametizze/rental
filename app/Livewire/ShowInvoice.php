<?php

namespace App\Livewire;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\Validate;

class ShowInvoice extends Component
{
    public Invoice $invoice;

    // Propriedades para o novo pagamento
    #[Validate('required|numeric|min:0.01')]
    public $newPaymentAmount;

    #[Validate('required|date')]
    public $newPaymentDate;

    #[Validate('nullable|string')]
    public $newPaymentNotes;

    public function mount(Invoice $invoice)
    {
        // Carrega todas as relações necessárias: cliente, itens, pagamentos e aluguel.
        $this->invoice = $invoice->load(['customer', 'items', 'payments', 'tenant', 'rental.equipment']);

        // Define a data de hoje como padrão para o pagamento
        $this->newPaymentDate = now()->format('m/d/Y');
    }

    public function addPayment()
    {
        $this->validate([
            'newPaymentAmount' => 'required|numeric|min:0.01',
            'newPaymentDate' => 'required|date',
            'newPaymentNotes' => 'nullable|string|max:255',
        ]);

        $balanceDue = $this->invoice->total - $this->invoice->paid_amount;

        if (round($this->newPaymentAmount, 2) > round($balanceDue, 2)) {
            session()->flash('error', __('The payment amount exceeds the balance due.'));
            return;
        }

        DB::beginTransaction();

        try {
            // 1. Cria o registro de pagamento (Histórico)
            $payment = Payment::create([
                'tenant_id' => $this->invoice->tenant_id,
                'invoice_id' => $this->invoice->id,
                'amount' => $this->newPaymentAmount,
                'payment_date' => $this->newPaymentDate,
                'notes' => $this->newPaymentNotes,
            ]);

            // 2. CRUCIAL: Cria a Transação de Receita (Unified Ledger)
            Transaction::create([
                'tenant_id' => $this->invoice->tenant_id,
                'type' => 'income',
                'amount' => $this->newPaymentAmount,
                'description' => 'Invoice Payment: #' . $this->invoice->uuid . ' (Partial)',
                'date' => $this->newPaymentDate,
                'source_id' => $payment->id, // Vincula à tabela de pagamentos
                'source_type' => Payment::class,
                'customer_id' => $this->invoice->customer_id,
                'status' => 'received', // O pagamento já ocorreu
            ]);

            // 3. Atualiza a fatura: Incrementa o valor pago e status
            $this->invoice->paid_amount += $this->newPaymentAmount;

            if (round($this->invoice->paid_amount, 2) >= round($this->invoice->total, 2)) {
                $this->invoice->status = 'paid';
            } else {
                $this->invoice->status = 'partially_paid';
            }

            $this->invoice->save();

            DB::commit();

            $this->invoice->refresh();
            session()->flash('success', __('Payment added successfully.'));
            $this->reset(['newPaymentAmount', 'newPaymentDate', 'newPaymentNotes']);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Transaction creation failed: ' . $e->getMessage());
            session()->flash('error', __('An error occurred. Please try again.'));
        }
    }

    public function render()
    {
        return view('livewire.show-invoice');
    }
}
