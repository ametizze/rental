<?php

namespace App\Livewire;

use App\Models\Rental;
use App\Models\Customer;
use App\Models\Equipment;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RentalManager extends Component
{
    use WithPagination, WithFileUploads;

    public $rentalId;
    public $customer_id, $start_date, $end_date;
    public $selected_equipment = [];
    public $rental_equipment = [];
    public $total_amount = 0;
    public $start_photos = [];

    protected $rules = [
        'customer_id' => 'required|exists:customers,id',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'selected_equipment' => 'required|array|min:1',
        'start_photos.*' => 'nullable|image|max:4096', // Fotos de até 4MB
    ];

    protected $messages = [
        'selected_equipment.min' => 'É necessário selecionar pelo menos um equipamento.',
    ];

    protected $listeners = ['refreshRentals' => '$refresh'];

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['start_date', 'end_date'])) {
            $this->calculateTotalAmount();
        }
    }

    public function calculateTotalAmount()
    {
        $this->total_amount = 0;
        if ($this->start_date && $this->end_date && count($this->selected_equipment) > 0) {
            $startDate = Carbon::parse($this->start_date);
            $endDate = Carbon::parse($this->end_date);
            $days = $startDate->diffInDays($endDate) + 1;

            $equipment = Equipment::find($this->selected_equipment);
            $total = $equipment->sum('daily_rate') * $days;

            $this->total_amount = $total;
        }
    }

    public function toggleEquipmentSelection($id)
    {
        $index = array_search($id, $this->selected_equipment);

        if ($index !== false) {
            unset($this->selected_equipment[$index]);
        } else {
            $this->selected_equipment[] = $id;
        }

        $this->selected_equipment = array_values($this->selected_equipment);
        $this->rental_equipment = Equipment::find($this->selected_equipment);
        $this->calculateTotalAmount();
    }

    public function save()
    {
        $this->validate();

        $photoPaths = [];
        foreach ($this->start_photos as $photo) {
            $photoPaths[] = $photo->store('rental_photos/start', 'public');
        }

        DB::beginTransaction();

        try {
            $rental = Rental::create([
                'customer_id' => $this->customer_id,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'total_amount' => $this->total_amount,
                'tenant_id' => auth()->user()->tenant_id,
                'start_photos' => $photoPaths,
            ]);

            $rental->equipment()->sync($this->selected_equipment);

            Equipment::whereIn('id', $this->selected_equipment)->update(['status' => 'rented']);

            DB::commit();
            session()->flash('success', __('Rental created successfully.'));
            $this->resetForm();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', __('An error occurred. Please try again.'));
        }
    }

    public function openCompleteRentalModal(int $rentalId)
    {
        $this->dispatch('openCompleteRentalModal', rentalId: $rentalId);
    }

    public function render()
    {
        $customers = Customer::all();
        $availableEquipment = Equipment::where('status', 'available')->get();

        $rentals = Rental::with('customer', 'equipment')->latest()->paginate(15);

        return view('livewire.rental-manager', [
            'customers' => $customers,
            'availableEquipment' => $availableEquipment,
            'rentals' => $rentals,
        ]);
    }

    public function resetForm()
    {
        $this->reset(['customer_id', 'start_date', 'end_date', 'selected_equipment', 'rental_equipment', 'total_amount', 'start_photos']);
    }
}
