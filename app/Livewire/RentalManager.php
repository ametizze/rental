<?php

namespace App\Livewire;

use App\Models\Rental;
use App\Models\Customer;
use App\Models\Equipment;
use App\Models\StockItem;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Carbon\Carbon;
use Illuminate\Container\Attributes\Log;
use Illuminate\Support\Facades\DB;
use Intervention\Image\ImageManager;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;

class RentalManager extends Component
{
    use WithPagination, WithFileUploads;

    public $rentalId;
    public $customer_id, $start_date, $end_date;
    public $selected_equipment = [];
    public $rental_equipment = [];
    public $total_amount = 0;
    public $startPhotos = [];

    public $stockItems = []; // List all stock items for consumables
    public $selectedConsumables = []; // Items that were added to the rental
    public $newConsumableId; // ID of the selected item in the dropdown
    public $newConsumableQuantity; // Quantity to be rented/sold

    public function rules()
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'selected_equipment' => 'required|array|min:1',
            'startPhotos.*.photo' => 'required|image|max:16384',
            'startPhotos.*.label' => 'nullable|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'selected_equipment.min' => __('Select at least one equipment.'),
            'startPhotos.*.photo.required' => __('Please take or select an image for this photo field.'),
            'startPhotos.*.photo.image' => __('The file selected must be a valid image format.'),
            'startPhotos.*.photo.max' => __('max_photo_size', ['size' => '16MB']),
        ];
    }

    protected $listeners = ['refreshRentals' => '$refresh'];

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['start_date', 'end_date'])) {
            $this->calculateTotalAmount();
        }
    }

    public function calculateTotalAmount()
    {
        $equipmentTotal = 0;
        $consumablesTotal = 0;

        if ($this->start_date && $this->end_date && count($this->selected_equipment) > 0) {
            $days = Carbon::parse($this->start_date)->diffInDays(Carbon::parse($this->end_date)) + 1;
            $equipmentTotal = Equipment::find($this->selected_equipment)->sum('daily_rate') * $days;
        }

        // Soma o total dos consumíveis selecionados
        $consumablesTotal = collect($this->selectedConsumables)->sum('amount');

        $this->total_amount = $equipmentTotal + $consumablesTotal;
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

    public function mount()
    {
        $this->addPhotoField();
        $this->loadStockItems();

        $this->stockItems = StockItem::all();
    }

    public function addPhotoField()
    {
        $this->startPhotos[] = ['photo' => null, 'label' => null];
    }

    public function removePhotoField($index)
    {
        if (isset($this->startPhotos[$index])) {
            $this->resetErrorBag('startPhotos.' . $index . '.photo');
            unset($this->startPhotos[$index]);
            $this->startPhotos = array_values($this->startPhotos);
        }
    }

    public function save()
    {
        $this->validate();

        $photoPaths = [];
        // 1. Lógica de compressão para cada foto
        foreach ($this->startPhotos as $key => $photoBlock) {
            if (isset($photoBlock['photo'])) {
                // Cria um gerenciador de imagem
                $manager = new ImageManager(new Driver());

                // Lê o arquivo temporário do Livewire
                $image = $manager->read($photoBlock['photo']->getRealPath());

                // Redimensiona a imagem para uma largura máxima de 1024px, mantendo a proporção.
                // A função 'scaleDown' é ideal, pois só redimensiona se a imagem for maior que o limite.
                $image->scaleDown(width: 1024);

                // Gera um nome de arquivo único
                $filename = Str::random(40) . '.' . $photoBlock['photo']->getClientOriginalExtension();
                $path = 'rental_photos/start/' . $filename;

                // Salva a imagem otimizada no disco 'public'
                $image->save(storage_path('app/public/' . $path));

                // Armazena o caminho otimizado e a label
                $photoPaths[] = [
                    'path' => $path,
                    'label' => $photoBlock['label'],
                    'timestamp' => now()->toDateTimeString(),
                ];
            }
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
                'uuid' => (string) Str::uuid(),
                'consumables_log' => $this->selectedConsumables,
            ]);

            $rental->equipment()->sync($this->selected_equipment);

            Equipment::whereIn('id', $this->selected_equipment)->update(['status' => 'rented']);

            foreach ($this->selectedConsumables as $consumable) {
                // Diminuir a quantidade no estoque
                StockItem::where('id', $consumable['stock_item_id'])->decrement('quantity', $consumable['quantity']);

                // Nota: O item consumível será adicionado à fatura no momento da criação da fatura,
                // mas você pode salvá-lo em uma coluna JSON no Rental ou em uma tabela pivot.
            }

            DB::commit();
            session()->flash('success', __('Rental created successfully.'));
            $this->resetForm();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating rental: ' . $e->getMessage());
            session()->flash('error', __('An error occurred. Please try again.'));
        }
    }

    public function openCompleteRentalModal(int $rentalId)
    {
        $this->dispatch('openCompleteRentalModal', rentalId: $rentalId);
    }

    public function loadStockItems()
    {
        $this->stockItems = StockItem::all();
    }

    public function addConsumable()
    {
        $this->validate([
            'newConsumableId' => 'required|exists:stock_items,id',
            'newConsumableQuantity' => 'required|integer|min:1',
        ]);

        $item = $this->stockItems->find($this->newConsumableId);

        // Validação de saldo em estoque
        if ($this->newConsumableQuantity > $item->quantity) {
            session()->flash('error', __('Stock is insufficient for this item.'));
            return;
        }

        $this->selectedConsumables[] = [
            'stock_item_id' => $item->id,
            'name' => $item->name,
            'unit' => $item->unit,
            'unit_price' => $item->unit_price,
            'quantity' => $this->newConsumableQuantity,
            'amount' => $item->unit_price * $this->newConsumableQuantity,
        ];

        $this->calculateTotalAmount();
        $this->reset(['newConsumableId', 'newConsumableQuantity']);
    }

    public function removeConsumable($index)
    {
        unset($this->selectedConsumables[$index]);
        $this->selectedConsumables = array_values($this->selectedConsumables);
        $this->calculateTotalAmount();
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
        $this->reset(['customer_id', 'start_date', 'end_date', 'selected_equipment', 'rental_equipment', 'total_amount', 'startPhotos']);
        $this->addPhotoField(); // Garante que o primeiro campo de foto esteja presente
    }
}
