<?php

namespace App\Livewire;

use App\Models\StockItem;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class StockItemManager extends Component
{
    use WithPagination, WithFileUploads;

    public $itemId;
    public $name, $unitPrice, $quantity, $unit, $referenceCode;
    public $photo;
    public $existingPhoto;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'unitPrice' => 'required|numeric|min:0.01',
            'quantity' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            // Reference code must be unique per tenant (we rely on HasTenant scope here)
            'referenceCode' => 'nullable|string|max:50|unique:stock_items,reference_code,' . $this->itemId,
            'photo' => 'nullable|image|max:16384',
        ];
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'unit_price' => $this->unitPrice,
            'quantity' => $this->quantity,
            'unit' => $this->unit,
            'reference_code' => $this->referenceCode,
            'tenant_id' => auth()->user()->tenant_id,
        ];

        // Lógica de Compressão e Upload de Foto
        if ($this->photo) {
            $manager = new ImageManager(new Driver());
            $image = $manager->read($this->photo->getRealPath());
            $image->scaleDown(width: 800);

            $filename = Str::random(40) . '.' . $this->photo->getClientOriginalExtension();
            $path = 'stock_photos/' . $filename;
            $image->save(storage_path('app/public/' . $path));

            $data['photo_path'] = $path;
        }

        StockItem::updateOrCreate(['id' => $this->itemId], $data);
        session()->flash('success', $this->itemId ? __('Stock item updated successfully.') : __('Stock item created successfully.'));
        $this->resetForm();
    }

    public function edit(StockItem $item)
    {
        $this->itemId = $item->id;
        $this->name = $item->name;
        $this->unitPrice = $item->unit_price;
        $this->quantity = $item->quantity;
        $this->unit = $item->unit;
        $this->referenceCode = $item->reference_code;
        $this->existingPhoto = $item->photo_path;
    }

    public function delete($id)
    {
        StockItem::destroy($id);
        session()->flash('success', __('Stock item deleted successfully.'));
    }

    public function resetForm()
    {
        $this->reset(['itemId', 'name', 'unitPrice', 'quantity', 'unit', 'referenceCode', 'photo', 'existingPhoto']);
    }

    public function render()
    {
        $stockItems = StockItem::latest()->paginate(10);
        return view('livewire.stock-item-manager', [
            'stockItems' => $stockItems,
        ]);
    }
}
