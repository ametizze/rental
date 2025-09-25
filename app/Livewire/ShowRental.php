<?php

namespace App\Livewire;

use App\Models\Rental;
use Livewire\Component;

class ShowRental extends Component
{
    public $rental;

    public function mount(string $rental)
    {
        $this->rental = Rental::with(['customer', 'equipment'])
            ->where('uuid', $rental)
            ->firstOrFail();
    }

    public function render()
    {
        return view('livewire.show-rental');
    }
}
