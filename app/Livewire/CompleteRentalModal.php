<?php

namespace App\Livewire;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Rental;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

class CompleteRentalModal extends Component
{
    use WithFileUploads;

    public $show = false;
    public ?Rental $rental = null;
    public $end_photos = [];

    protected $listeners = ['openCompleteRentalModal'];

    protected $rules = [
        'end_photos.*' => 'nullable|image|max:2048',
    ];

    public function openCompleteRentalModal(int $rentalId)
    {
        $this->rental = Rental::with('customer', 'equipment')->find($rentalId);
        $this->show = true;
    }

    public function completeRental()
    {
        $this->validate();

        DB::beginTransaction();

        try {
            if ($this->rental->status !== 'active') {
                session()->flash('error', __('This rental is not active.'));
                $this->close();
                return;
            }

            // 1. Lida com as fotos de devolução
            $photoPaths = [];
            foreach ($this->end_photos as $photo) {
                $photoPaths[] = $photo->store('rental_photos/end', 'public');
            }

            // 2. Atualiza o status do aluguel e as fotos
            $this->rental->update([
                'status' => 'completed',
                'end_photos' => $photoPaths,
            ]);

            // 3. Atualiza o status dos equipamentos
            $this->rental->equipment()->update(['status' => 'available']);

            // 4. Cria a fatura automaticamente
            $tenantSettings = json_decode($this->rental->tenant->settings, true);
            $taxRate = $tenantSettings['tax_rate'] ?? 0;

            $invoice = Invoice::create([
                'uuid' => (string) Str::uuid(),
                'tenant_id' => $this->rental->tenant_id,
                'customer_id' => $this->rental->customer_id,
                'bill_to_name' => $this->rental->customer->name,
                'bill_to_email' => $this->rental->customer->email,
                'bill_to_phone' => $this->rental->customer->phone,
                'tax_rate' => $taxRate,
                'subtotal' => $this->rental->total_amount, // Ajuste para subtrair impostos do total
                'tax_amount' => $this->rental->total_amount * $taxRate,
                'total' => $this->rental->total_amount + ($this->rental->total_amount * $taxRate),
                'due_date' => now()->addDays(7),
                'notes' => __('Invoice automatically generated from rental.') . ' #' . $this->rental->id,
                'photos' => $photoPaths,
                'status' => 'unpaid'
            ]);

            // 5. Adiciona os itens da fatura
            foreach ($this->rental->equipment as $equipment) {
                $days = $this->rental->start_date->diffInDays($this->rental->end_date) + 1;
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => $equipment->name . ' (' . $equipment->serial . ')',
                    'quantity' => $days,
                    'rate' => $equipment->daily_rate,
                    'amount' => $equipment->daily_rate * $days,
                ]);
            }

            DB::commit();
            session()->flash('success', __('Rental marked as completed and invoice created successfully.'));

            $this->close();
            $this->dispatch('refreshRentals');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', __('An error occurred while finalizing the rental and creating the invoice.'));
        }
    }

    public function close()
    {
        $this->show = false;
        $this->end_photos = [];
        $this->rental = null;
    }

    public function render()
    {
        return view('livewire.complete-rental-modal');
    }
}
