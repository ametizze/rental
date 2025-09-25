<?php

namespace App\Livewire;

namespace App\Livewire;

use App\Models\TransactionCategory;
use Livewire\Component;
use Livewire\WithPagination;

class TransactionCategoryManager extends Component
{
    use WithPagination;

    // Propriedades do formulário
    public $categoryId;
    public $name = '';
    public $type = 'expense'; // 'income' ou 'expense'

    protected $rules = [
        'name' => 'required|string|max:100',
        'type' => 'required|in:income,expense',
    ];

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'type' => $this->type,
            'tenant_id' => auth()->user()->tenant_id,
        ];

        // Cria ou atualiza a categoria
        TransactionCategory::updateOrCreate(['id' => $this->categoryId], $data);

        session()->flash('success', $this->categoryId ? 'Category updated successfully!' : 'Category created successfully!');

        $this->resetForm();
    }

    public function edit(TransactionCategory $category)
    {
        $this->categoryId = $category->id;
        $this->name = $category->name;
        $this->type = $category->type;
    }

    public function delete(TransactionCategory $category)
    {
        // Ao deletar, as transações associadas terão seu category_id definido como NULL (via onDelete('set null') na migration)
        $category->delete();
        session()->flash('success', 'Category deleted successfully!');
    }

    public function resetForm()
    {
        $this->reset(['categoryId', 'name', 'type']);
        $this->type = 'expense';
    }

    public function render()
    {
        $categories = TransactionCategory::latest()->paginate(10);

        return view('livewire.transaction-category-manager', [
            'categories' => $categories,
        ]);
    }
}
