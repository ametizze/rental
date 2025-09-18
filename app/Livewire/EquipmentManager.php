<?php
// app/Livewire/EquipmentManager.php

namespace App\Livewire;

use App\Models\Equipment;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class EquipmentManager extends Component
{
    use WithFileUploads;

    public $equipmentId;
    public $name, $category, $serial, $daily_rate, $photo, $status = 'available';
    public $qrCode;

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
        // Regra de validação para o serial, garantindo que ele seja único,
        // mas permitindo que o serial atual do equipamento editado seja usado.
        $this->rules['serial'] = 'required|string|unique:equipment,serial,' . $this->equipmentId;

        $this->validate();

        $data = [
            'name' => $this->name,
            'category' => $this->category,
            'serial' => $this->serial,
            'daily_rate' => $this->daily_rate,
            'status' => $this->status,
        ];

        // Se estivermos editando, não altere o QR UUID.
        // Se estivermos criando, gere um novo.
        if (!$this->equipmentId) {
            $data['qr_uuid'] = (string) Str::uuid();
            $data['tenant_id'] = auth()->user()->tenant_id;
        }

        // Lida com o upload de foto
        if ($this->photo) {
            $data['photo'] = $this->photo->store('equipment_photos', 'public');
        }

        Equipment::updateOrCreate(['id' => $this->equipmentId], $data);

        session()->flash('success', $this->equipmentId ? 'Equipamento atualizado com sucesso!' : 'Equipamento criado com sucesso!');

        // Resetar o formulário após o salvamento
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

    public function resetForm()
    {
        $this->reset(['name', 'category', 'serial', 'daily_rate', 'photo', 'status', 'equipmentId', 'existingPhoto']);
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
