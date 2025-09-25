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
        $this->validate([
            'newPaymentAmount' => 'required|numeric|min:0.01',
            'newPaymentDate' => 'required|date',
            'newPaymentNotes' => 'nullable|string|max:255',
        ]);

        // Calcula o Saldo Devedor
        $balanceDue = $this->invoice->total - $this->invoice->paid_amount;

        // 1. Validação Crítica: Não aceitar pagamento maior que o saldo
        if (round($this->newPaymentAmount, 2) > round($balanceDue, 2)) {
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

        // 3. Atualiza a fatura: Incrementa o valor pago
        $this->invoice->paid_amount += $this->newPaymentAmount;

        // 4. Define o Status Final
        if (round($this->invoice->paid_amount, 2) >= round($this->invoice->total, 2)) {
            $this->invoice->status = 'paid';
        } else {
            $this->invoice->status = 'partially_paid';
        }

        $this->invoice->save();

        // 5. Recarrega o componente (a si próprio) para atualizar as tabelas e o saldo
        $this->invoice->refresh();

        session()->flash('success', __('Payment added successfully.'));
        $this->reset(['newPaymentAmount', 'newPaymentDate', 'newPaymentNotes']);
    }

    public function render()
    {
        return view('livewire.show-invoice');
    }
}
