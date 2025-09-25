<?php
// app/Livewire/TransactionManager.php

namespace App\Livewire;

use App\Models\Transaction;
use App\Models\TransactionCategory;
use App\Models\Customer;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class TransactionManager extends Component
{
    use WithPagination;

    protected $listeners = ['categorySaved' => 'reloadCategories'];

    public $transactionId;
    public $type = 'expense';
    public $amount;
    public $description;
    public $date;
    public $categoryId;
    public $customerId;
    public $dueDate;

    protected function rules()
    {
        return [
            'type' => 'required|string|in:income,expense',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'date' => 'required|date',
            'categoryId' => 'required|exists:transaction_categories,id',
            'customerId' => 'nullable|exists:customers,id',
            // dueDate é obrigatório se for receita, mas nullable no banco de dados.
            'dueDate' => [($this->type === 'income' ? 'required' : 'nullable'), 'date', 'after_or_equal:date'],
        ];
    }

    public function mount()
    {
        $this->date = Carbon::today()->format('Y-m-d');
        // Define uma data de vencimento padrão para 30 dias para novas receitas
        $this->dueDate = Carbon::today()->addDays(30)->format('Y-m-d');
    }

    public function save()
    {
        $this->validate();

        $data = [
            'type' => $this->type,
            'amount' => $this->amount,
            'description' => $this->description,
            'date' => $this->date,
            'category_id' => $this->categoryId,
            'customer_id' => $this->customerId,
            // dueDate só é salvo se for receita, caso contrário é null
            'due_date' => ($this->type === 'income') ? $this->dueDate : null,
            'tenant_id' => auth()->user()->tenant_id,
            'status' => ($this->type === 'income') ? 'pending' : 'received', // Receitas pendentes, Despesas recebidas
        ];

        Transaction::updateOrCreate(['id' => $this->transactionId], $data);

        session()->flash('success', $this->transactionId ? 'Transaction updated successfully!' : 'Transaction created successfully!');
        $this->resetForm();
    }

    // ... (edit method ajustado para carregar novos campos)
    public function edit(Transaction $transaction)
    {
        $this->transactionId = $transaction->id;
        $this->type = $transaction->type;
        $this->amount = $transaction->amount;
        $this->description = $transaction->description;
        $this->date = $transaction->date->format('Y-m-d');
        $this->categoryId = $transaction->category_id;
        $this->customerId = $transaction->customer_id;
        $this->dueDate = $transaction->due_date ? $transaction->due_date->format('Y-m-d') : null;
    }

    public function render()
    {
        // Filtra categorias pelo tipo da transação atual
        $categories = TransactionCategory::all(); // Esta linha recarrega
        $customers = Customer::all();
        $transactions = Transaction::with(['category', 'customer'])->latest('date')->paginate(10);

        return view('livewire.transaction-manager', [
            'transactions' => $transactions,
            'categories' => $categories,
            'customers' => $customers,
        ]);
    }

    public function reloadCategories()
    {
        // Força a atualização da propriedade $categories na próxima renderização
        // Livewire faz isso automaticamente se a propriedade não for definida aqui.
    }

    // ... (resetForm ajustado para novos campos)
    public function resetForm()
    {
        $this->reset(['transactionId', 'amount', 'description', 'categoryId', 'customerId', 'dueDate']);
        $this->type = 'expense'; // Mantém o último tipo selecionado
        $this->date = Carbon::today()->format('Y-m-d');
        $this->dueDate = Carbon::today()->addDays(30)->format('Y-m-d');
    }
}
