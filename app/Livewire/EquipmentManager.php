<?php

namespace App\Livewire;

use App\Models\Equipment;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class EquipmentManager extends Component
{
    use WithFileUploads;

    public $equipmentId;
    public $name, $category, $serial, $daily_rate, $status = 'available';
    public $qrCode;
    public $existingPhoto;

    public $initialCost;
    public $purchaseDate;

    /**
     * Photo validation: allow up to 32MB (32768 KB).
     * Note: Laravel's "max" for files is in kilobytes.
     */
    public $photo;

    protected function rules()
    {
        return [
            'name'       => 'required|string|max:255',
            'category'   => 'nullable|string|max:255',
            'serial'     => 'required|string|unique:equipment,serial,' . $this->equipmentId,
            'daily_rate' => 'required|numeric|min:0',
            'photo'      => 'nullable|image|mimes:png,jpg,jpeg,heic|max:16384', // Max 16mb
            'status'     => 'required|string',
            'initialCost' => 'required|numeric|min:0', // Novo
            'purchaseDate' => 'required|date',        // Novo
        ];
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'category' => $this->category,
            'serial' => $this->serial,
            'daily_rate' => $this->daily_rate,
            'initial_cost' => $this->initialCost, // Salva o custo inicial
            'purchase_date' => $this->purchaseDate, // Salva a data de compra
            'status' => $this->status,
        ];

        if (!$this->equipmentId) {
            $data['qr_uuid'] = (string) Str::uuid();
            $data['tenant_id'] = auth()->user()->tenant_id;
        }

        if ($this->photo) {
            $manager = new ImageManager(new Driver());
            $image = $manager->read($this->photo->getRealPath());
            $image->scaleDown(width: 1024);
            $filename = Str::random(40) . '.' . $this->photo->getClientOriginalExtension();
            $path = 'equipment_photos/' . $filename;
            $image->save(storage_path('app/public/' . $path));
            $data['photo'] = $path;
        }

        Equipment::updateOrCreate(['id' => $this->equipmentId], $data);
        session()->flash('success', $this->equipmentId ? 'Equipamento atualizado com sucesso!' : 'Equipamento criado com sucesso!');
        $this->resetForm();
    }

    public function edit(Equipment $equipment)
    {
        $this->equipmentId = $equipment->id;
        $this->name = $equipment->name;
        $this->category = $equipment->category;
        $this->serial = $equipment->serial;
        $this->daily_rate = $equipment->daily_rate;
        $this->status = $equipment->status;
        $this->existingPhoto = $equipment->photo;

        // Carrega os novos campos
        $this->initialCost = $equipment->initial_cost;
        $this->purchaseDate = $equipment->purchase_date ? $equipment->purchase_date->format('Y-m-d') : null;
    }

    public function resetForm()
    {
        $this->reset([
            'name',
            'category',
            'serial',
            'daily_rate',
            'photo',
            'status',
            'equipmentId',
            'existingPhoto',
            'initialCost',
            'purchaseDate'
        ]);
    }

    public function delete($id)
    {
        Equipment::destroy($id);
    }

    public function render()
    {
        $equipment = Equipment::all();

        return view('livewire.equipment-manager', [
            'equipment' => $equipment,
        ]);
    }
}
