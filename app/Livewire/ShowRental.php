<?php

namespace App\Livewire;

use App\Models\Rental;
use Livewire\Component;

class ShowRental extends Component
{
    public $rental;

    public function mount(string $uuid)
    {
        $this->rental = Rental::with(['customer', 'equipment'])
            ->where('uuid', $uuid)
            ->firstOrFail();
    }

    public function render()
    {
        return view('livewire.show-rental');
    }
}
