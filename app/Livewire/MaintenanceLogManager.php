<?php

namespace App\Livewire;

use App\Models\Equipment;
use App\Models\MaintenanceLog;
use App\Models\Transaction;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
        $this->date = Carbon::today()->format('m/d/Y');
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

        // Usaremos uma transação DB para garantir que ambos os registros sejam criados ou nenhum seja.
        DB::beginTransaction();

        try {
            $log = MaintenanceLog::updateOrCreate(['id' => $this->logId], $data);

            // CRUCIAL: Cria a Transação de Despesa (Expense)
            Transaction::create([
                'tenant_id' => $log->tenant_id,
                'type' => 'expense',
                'amount' => $log->cost,
                'description' => 'Maintenance Cost for: ' . ($log->equipment->name ?? 'N/A'),
                'date' => $log->date,
                'category_id' => null, // Assumindo que a categoria de manutenção será definida em outro lugar
                'equipment_id' => $log->equipment_id,
                'source_id' => $log->id,
                'source_type' => MaintenanceLog::class,
                'status' => 'received', // Despesa é sempre 'received' quando paga
            ]);

            DB::commit();
            session()->flash('success', $this->logId ? __('Log updated successfully!') : __('Log created successfully!'));
            $this->resetForm();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Transaction creation failed: ' . $e->getMessage());
            session()->flash('error', __('An error occurred. Please try again.'));
        }
    }

    public function edit(MaintenanceLog $log)
    {
        $this->logId = $log->id;
        $this->equipmentId = $log->equipment_id;
        $this->cost = $log->cost;
        $this->description = $log->description;
        $this->date = $log->date->format('m/d/Y');
    }

    public function delete($id)
    {
        MaintenanceLog::destroy($id);
        session()->flash('success', __('Log deleted successfully!'));
    }

    public function resetForm()
    {
        $this->reset(['logId', 'equipmentId', 'cost', 'description', 'date']);
        $this->date = Carbon::today()->format('m/d/Y');
    }

    public function render()
    {
        $logs = MaintenanceLog::with('equipment')->latest('date')->paginate(10);

        return view('livewire.maintenance-log-manager', [
            'logs' => $logs,
        ]);
    }
}
