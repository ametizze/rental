<?php

namespace App\Livewire;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Rental;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class CompleteRentalModal extends Component
{
    use WithFileUploads;

    public $show = false;
    public ?Rental $rental = null;
    public $endPhotos = [];

    protected $listeners = ['openCompleteRentalModal'];

    protected function rules()
    {
        return [
            'endPhotos.*.photo' => 'required|image|max:16384',
            'endPhotos.*.label' => 'nullable|string|max:100',
        ];
    }

    public function mount()
    {
        $this->addPhotoField();
    }

    public function addPhotoField()
    {
        $this->endPhotos[] = ['photo' => null, 'label' => null];
    }

    public function removePhotoField($index)
    {
        if (isset($this->endPhotos[$index])) {
            unset($this->endPhotos[$index]);
            $this->endPhotos = array_values($this->endPhotos);
        }
    }

    public function openCompleteRentalModal(int $rentalId)
    {
        $this->rental = Rental::with('customer', 'equipment')->find($rentalId);
        $this->show = true;
    }

    public function completeRental()
    {
        // 1. Validação com geração de nomes amigáveis
        $this->validate(
            $this->rules(),
            [], // Mensagens globais
            $this->getValidationAttributes() // Nomes amigáveis
        );

        DB::beginTransaction();

        try {
            if ($this->rental->status !== 'active') {
                session()->flash('error', __('This rental is not active.'));
                $this->close();
                return;
            }

            // 1. Lida com as fotos de devolução
            $photoPaths = [];
            foreach ($this->endPhotos as $photoBlock) {
                if (isset($photoBlock['photo'])) {
                    $manager = new ImageManager(new Driver());
                    $image = $manager->read($photoBlock['photo']->getRealPath());
                    $image->scaleDown(width: 1024); // Comprime a imagem

                    $filename = Str::random(40) . '.' . $photoBlock['photo']->getClientOriginalExtension();
                    $path = 'rental_photos/end/' . $filename;
                    $image->save(storage_path('app/public/' . $path));

                    $photoPaths[] = [
                        'path' => $path,
                        'label' => $photoBlock['label'],
                        'timestamp' => now()->toDateTimeString(),
                    ];
                }
            }

            // 2. Atualiza o status do aluguel e as fotos
            $this->rental->update([
                'status' => 'completed',
                'end_photos' => $photoPaths, // Salva o array JSON de caminhos + rótulos
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
            \Log::error('Error completing rental: ' . $e->getMessage());
            DB::rollBack();
            session()->flash('error', __('An error occurred while finalizing the rental and creating the invoice.'));
        }
    }

    public function getValidationAttributes()
    {
        $attributes = [];
        foreach ($this->endPhotos as $index => $photoBlock) {
            $number = $index + 1;
            $attributes["endPhotos.{$index}.photo"] = __('Return Photo') . ' ' . $number;
            $attributes["endPhotos.{$index}.label"] = __('Return Label') . ' ' . $number;
        }
        return $attributes;
    }

    public function close()
    {
        $this->show = false;
        $this->endPhotos = [];
        $this->rental = null;
    }

    public function render()
    {
        return view('livewire.complete-rental-modal');
    }
}
