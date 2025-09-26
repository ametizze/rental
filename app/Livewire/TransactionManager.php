<?php

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

    // Form properties
    public $transactionId;
    public $type = '';
    public $amount;
    public $description;
    public $date;
    public $categoryId;
    public $customerId;
    public $dueDate;
    public $status; // CRUCIAL: Status is editable in the form

    // Properties for Search and Filters
    public $search = '';
    public $filterType = '';
    public $filterStatus = '';

    // Options for the Status dropdown (Overdue is calculated, not set directly)
    public $statusOptions = ['pending', 'received', 'return', 'archived'];
    public $typeOptions = ['income', 'expense'];

    protected $queryString = [
        'search' => ['except' => ''],
        'filterType' => ['except' => ''],
        'filterStatus' => ['except' => ''],
    ];

    protected function rules()
    {
        return [
            'type' => 'required|string|in:income,expense',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'date' => 'required|date',
            'categoryId' => 'required|exists:transaction_categories,id',
            'customerId' => 'nullable|exists:customers,id',
            // Due Date: Required for income, optional for expense
            'dueDate' => [($this->type === 'income' ? 'required' : 'nullable'), 'date', 'after_or_equal:date'],
            'status' => 'required|string|in:pending,received,return,archived', // Validate status from form
        ];
    }

    public function mount()
    {
        $this->date = Carbon::today()->format('m/d/Y');
        $this->status = 'pending'; // Default status
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingFilterType()
    {
        $this->resetPage();
    }
    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    /**
     * Saves or updates a transaction record.
     */
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
            'due_date' => ($this->type === 'income') ? $this->dueDate : null,
            'tenant_id' => auth()->user()->tenant_id,
            'status' => $this->status,
        ];

        Transaction::updateOrCreate(['id' => $this->transactionId], $data);

        session()->flash('success', $this->transactionId ? __('Transaction updated successfully!') : __('Transaction created successfully!'));
        $this->resetForm();
    }

    /**
     * Loads transaction data into the form for editing.
     */
    public function edit(Transaction $transaction)
    {
        $this->transactionId = $transaction->id;
        $this->type = $transaction->type;
        $this->amount = $transaction->amount;
        $this->description = $transaction->description;
        $this->date = $transaction->date->format('m/d/Y');
        $this->categoryId = $transaction->category_id;
        $this->customerId = $transaction->customer_id;
        $this->dueDate = $transaction->due_date ? $transaction->due_date->format('m/d/Y') : null;
        $this->status = $transaction->status; // Load existing status into the form
    }

    /**
     * Quick action to mark a pending/overdue income as 'received'.
     */
    public function markReceived(int $transactionId)
    {
        Transaction::where('id', $transactionId)->update(['status' => 'received']);
        session()->flash('success', 'Transaction marked as received.');
    }

    /**
     * Deletes a transaction record.
     */
    public function delete($id)
    {
        Transaction::destroy($id);
        session()->flash('success', 'Transaction deleted successfully!');
    }

    public function resetForm()
    {
        $this->reset(['transactionId', 'amount', 'description', 'categoryId', 'customerId', 'dueDate', 'status']);
        $this->type = 'expense';
        $this->date = Carbon::today()->format('m/d/Y');
    }

    public function render()
    {
        $categories = TransactionCategory::all();
        $customers = Customer::all();

        $transactionsQuery = Transaction::with(['category', 'customer'])
            ->latest('date');

        // Apply Search and Filters
        if ($this->search) {
            $transactionsQuery->where(function ($query) {
                $query->where('description', 'like', '%' . $this->search . '%')
                    ->orWhereHas('customer', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
            });
        }
        if ($this->filterType) {
            $transactionsQuery->where('type', $this->filterType);
        }

        if ($this->filterStatus) {
            $status = $this->filterStatus;

            if ($status === 'overdue') {
                $transactionsQuery->where('type', 'income')
                    ->where('status', 'pending')
                    ->whereDate('due_date', '<', Carbon::today());
            } else {
                $transactionsQuery->where('status', $status);
            }
        }

        $transactions = $transactionsQuery->paginate(10);

        return view('livewire.transaction-manager', [
            'transactions' => $transactions,
            'categories' => $categories,
            'customers' => $customers,
        ]);
    }
}
