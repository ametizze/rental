<?php

namespace App\Livewire;

use App\Models\Equipment;
use App\Models\MaintenanceLog;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class MaintenanceLogManager extends Component
{
    use WithPagination;

    public $logId;
    public $equipmentId;
    public $cost;
    public $description;
    public $date;
    public $equipments; // Lista de equipamentos para o dropdown

    protected $rules = [
        'equipmentId' => 'required|exists:equipment,id',
        'cost' => 'required|numeric|min:0.01',
        'description' => 'required|string|max:255',
        'date' => 'required|date',
    ];

    public function mount()
    {
        // Carrega a lista de equipamentos para o formulário
        $this->equipments = Equipment::all();
        $this->date = Carbon::today()->format('Y-m-d');
    }

    public function save()
    {
        $this->validate();

        $data = [
            'equipment_id' => $this->equipmentId,
            'cost' => $this->cost,
            'description' => $this->description,
            'date' => $this->date,
            'tenant_id' => auth()->user()->tenant_id,
        ];

        MaintenanceLog::updateOrCreate(['id' => $this->logId], $data);

        session()->flash('success', $this->logId ? __('Log updated successfully!') : __('Log created successfully!'));
        $this->resetForm();
    }

    public function edit(MaintenanceLog $log)
    {
        $this->logId = $log->id;
        $this->equipmentId = $log->equipment_id;
        $this->cost = $log->cost;
        $this->description = $log->description;
        $this->date = $log->date->format('Y-m-d');
    }

    public function delete($id)
    {
        MaintenanceLog::destroy($id);
        session()->flash('success', __('Log deleted successfully!'));
    }

    public function resetForm()
    {
        $this->reset(['logId', 'equipmentId', 'cost', 'description', 'date']);
        $this->date = Carbon::today()->format('Y-m-d');
    }

    public function render()
    {
        $logs = MaintenanceLog::with('equipment')->latest('date')->paginate(10);

        return view('livewire.maintenance-log-manager', [
            'logs' => $logs,
        ]);
    }
}
