<?php

namespace App\Livewire;

use App\Models\TransactionCategory;
use Livewire\Component;
use Livewire\Attributes\On;

class CategoryModal extends Component
{
    public $show = false;
    public $name = '';
    public $type = 'expense';
    public $categoryId;

    protected $rules = [
        'name' => 'required|string|max:100',
        'type' => 'required|in:income,expense',
    ];

    #[On('open-category-modal')]
    public function openCategoryModeal()
    {
        $this->show = true;
        $this->resetForm();
    }

    public function save()
    {
        $this->validate();

        TransactionCategory::create([
            'name' => $this->name,
            'type' => $this->type,
            'tenant_id' => auth()->user()->tenant_id,
        ]);

        session()->flash('success', 'Category created successfully!');

        // Fecha o modal e notifica o componente TransactionManager para recarregar as categorias
        $this->dispatch('categorySaved');
        $this->dispatch('close-category-modal'); // Dispara evento JS para a view fechar
        $this->close();
    }

    public function close()
    {
        $this->show = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset(['name', 'type', 'categoryId']);
        $this->type = 'expense';
    }

    public function render()
    {
        return view('livewire.category-modal');
    }
}
