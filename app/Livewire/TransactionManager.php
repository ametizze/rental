<?php

namespace App\Livewire;

use App\Models\Transaction;
use Livewire\Component;
use Livewire\WithPagination;

class TransactionManager extends Component
{
    use WithPagination;

    public $transactionId;
    public $type = 'expense';
    public $amount;
    public $description;
    public $date;

    protected $rules = [
        'type' => 'required|string|in:income,expense',
        'amount' => 'required|numeric|min:0.01',
        'description' => 'required|string|max:255',
        'date' => 'required|date',
    ];

    public function save()
    {
        $this->validate();

        $data = [
            'type' => $this->type,
            'amount' => $this->amount,
            'description' => $this->description,
            'date' => $this->date,
            'tenant_id' => auth()->user()->tenant_id,
        ];

        Transaction::updateOrCreate(['id' => $this->transactionId], $data);

        session()->flash('success', $this->transactionId ? 'Transação atualizada com sucesso!' : 'Transação criada com sucesso!');
        $this->resetForm();
    }

    public function edit(Transaction $transaction)
    {
        $this->transactionId = $transaction->id;
        $this->type = $transaction->type;
        $this->amount = $transaction->amount;
        $this->description = $transaction->description;
        $this->date = $transaction->date;
    }

    public function delete($id)
    {
        Transaction::destroy($id);
        session()->flash('success', 'Transação excluída com sucesso!');
    }

    public function resetForm()
    {
        $this->reset(['transactionId', 'type', 'amount', 'description', 'date']);
        $this->type = 'expense';
    }

    public function render()
    {
        $transactions = Transaction::latest('date')->paginate(10);

        return view('livewire.transaction-manager', [
            'transactions' => $transactions,
        ]);
    }
}
