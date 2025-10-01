<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\Equipment;
use App\Models\Rental;
use Livewire\Component;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class QuickRentManager extends Component
{
    public $equipmentRef = '';
    public $customerId;
    public $customers;
    public $equipmentDetail; // To display confirmation details

    protected $rules = [
        'equipmentRef' => 'required|string|max:50',
        'customerId' => 'required|exists:customers,id',
    ];

    public function mount()
    {
        // Load all customers for the dropdown
        $this->customers = Customer::all();
    }

    /**
     * Looks up the equipment by its internal reference code.
     */
    public function lookupEquipment()
    {
        $this->validate(['equipmentRef' => $this->rules['equipmentRef']]);

        // Find the equipment by the new reference code (scoped by tenant)
        $this->equipmentDetail = Equipment::where('ref_code', $this->equipmentRef)
            ->where('status', 'available')
            ->first();

        if (!$this->equipmentDetail) {
            session()->flash('error', __('Equipment not found or is currently rented.'));
        }
    }

    /**
     * Creates a new rental record with minimal information (1-day implicit rental).
     */
    public function quickRent()
    {
        $this->lookupEquipment(); // Ensure equipment is loaded

        // Final validation
        $this->validate();

        // Check if equipment is actually found and available
        if (!$this->equipmentDetail || $this->equipmentDetail->status !== 'available') {
            session()->flash('error', __('Equipment is not available for rent.'));
            return;
        }

        DB::beginTransaction();
        try {
            // 1. Create the Rental (Minimal Data)
            $rental = Rental::create([
                'tenant_id' => auth()->user()->tenant_id,
                'customer_id' => $this->customerId,
                'start_date' => now(),
                'end_date' => now()->addDay(), // Implicit 1-day rental
                'total_amount' => $this->equipmentDetail->daily_rate,
                'status' => 'active',
                'uuid' => (string) Str::uuid(),
            ]);

            // 2. Link the Equipment (Many-to-Many sync)
            $rental->equipment()->sync([$this->equipmentDetail->id]);

            // 3. Update Equipment Status
            $this->equipmentDetail->update(['status' => 'rented']);

            DB::commit();
            session()->flash('success', __("Rental created successfully for") . ' ' . $this->equipmentDetail->name);
            $this->resetForm();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Quick Rent Failed: ' . $e->getMessage());
            session()->flash('error', __('An error occurred. Rental was not created.'));
        }
    }

    public function resetForm()
    {
        $this->reset(['equipmentRef', 'customerId', 'equipmentDetail']);
    }

    public function render()
    {
        return view('livewire.quick-rent-manager');
    }
}
