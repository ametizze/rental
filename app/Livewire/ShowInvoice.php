<?php

namespace App\Livewire;

use App\Models\Invoice;
use App\Models\Payment;
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
        $this->newPaymentDate = now()->format('Y-m-d');
    }

    public function addPayment()
    {
        $this->validate();

        $balanceDue = $this->invoice->total - $this->invoice->paid_amount;

        // 1. Validação de Regra de Negócio: Não aceitar pagamento maior que o saldo
        if ($this->newPaymentAmount > $balanceDue) {
            session()->flash('error', __('The payment amount exceeds the balance due.'));
            return;
        }

        // 2. Cria o registro de pagamento
        Payment::create([
            'tenant_id' => $this->invoice->tenant_id,
            'invoice_id' => $this->invoice->id,
            'amount' => $this->newPaymentAmount,
            'payment_date' => $this->newPaymentDate,
            'notes' => $this->newPaymentNotes,
        ]);

        // 3. Atualiza a fatura
        $this->invoice->paid_amount += $this->newPaymentAmount;

        if ($this->invoice->paid_amount >= $this->invoice->total) {
            $this->invoice->status = 'paid';
        } else {
            $this->invoice->status = 'partially_paid';
        }

        $this->invoice->save();

        // 4. Recarrega o componente para refletir o novo saldo e histórico
        $this->invoice->refresh();

        session()->flash('success', __('Payment added successfully.'));
        $this->reset(['newPaymentAmount', 'newPaymentDate', 'newPaymentNotes']);
    }

    public function render()
    {
        return view('livewire.show-invoice');
    }
}
