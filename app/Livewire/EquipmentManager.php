<?php
// app/Livewire/EquipmentManager.php

namespace App\Livewire;

use App\Models\Equipment;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

class EquipmentManager extends Component
{
    use WithFileUploads;

    public $equipmentId;
    public $name, $category, $serial, $daily_rate, $photo, $status = 'available';

    // Propriedade para a foto existente (para edição)
    public $existingPhoto;

    protected $rules = [
        'name' => 'required|string|max:255',
        'category' => 'nullable|string|max:255',
        'serial' => 'required|string|unique:equipment,serial',
        'daily_rate' => 'required|numeric|min:0',
        'status' => 'required|string',
        'photo' => 'nullable|image|max:1024', // 1MB Max
    ];

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'category' => $this->category,
            'serial' => $this->serial,
            'daily_rate' => $this->daily_rate,
            'status' => $this->status,
            'qr_uuid' => (string) Str::uuid(),
        ];

        // Lida com o upload de foto
        if ($this->photo) {
            $data['photo'] = $this->photo->store('equipment_photos', 'public');
        }

        Equipment::updateOrCreate(['id' => $this->equipmentId], $data);
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
    }

    public function delete($id)
    {
        Equipment::destroy($id);
    }

    public function resetForm()
    {
        $this->reset(['name', 'category', 'serial', 'daily_rate', 'photo', 'status', 'equipmentId', 'existingPhoto']);
    }

    public function render()
    {
        $equipment = Equipment::all();

        return view('livewire.equipment-manager', [
            'equipment' => $equipment,
        ]);
    }
}
