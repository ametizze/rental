<?php

namespace App\Livewire;

use App\Models\Equipment;
use App\Models\Transaction;
use App\Models\MaintenanceLog;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;

class StockProfitabilityReport extends Component
{
    use WithPagination; // Adiciona paginação ao relatório

    public $reportData = [];

    public function mount()
    {
        $this->generateReport();
    }

    public function generateReport()
    {
        $tenantId = auth()->user()->tenant_id;

        // 1. Receita Total de Aluguel por Equipamento
        // Soma as transações de 'income' que estão ligadas a um equipamento.
        $rentalRevenues = Transaction::select('equipment_id', DB::raw('SUM(amount) as total_revenue'))
            ->where('tenant_id', $tenantId)
            ->whereNotNull('equipment_id')
            ->where('type', 'income')
            ->groupBy('equipment_id')
            ->get()
            ->keyBy('equipment_id');

        // 2. Custos Totais de Manutenção por Equipamento
        $maintenanceCosts = MaintenanceLog::select('equipment_id', DB::raw('SUM(cost) as total_maintenance_cost'))
            ->where('tenant_id', $tenantId)
            ->groupBy('equipment_id')
            ->get()
            ->keyBy('equipment_id');

        // 3. Compilação do Relatório
        // Filtra os equipamentos apenas do tenant atual (HasTenant já está ativo)
        $equipments = Equipment::all();
        $reportData = [];

        foreach ($equipments as $equipment) {
            $revenue = $rentalRevenues->get($equipment->id)['total_revenue'] ?? 0;
            $maintenance = $maintenanceCosts->get($equipment->id)['total_maintenance_cost'] ?? 0;
            $initialCost = $equipment->initial_cost;

            // Cálculos
            $totalCost = $initialCost + $maintenance;
            $netProfit = $revenue - $totalCost;
            $roi = $initialCost > 0 ? ($netProfit / $initialCost) * 100 : 0;

            $reportData[] = [
                'name' => $equipment->name,
                'initial_cost' => $initialCost,
                'total_revenue' => $revenue,
                'total_maintenance_cost' => $maintenance,
                'net_profit' => $netProfit,
                'roi' => $roi,
            ];
        }

        $this->reportData = $reportData;
    }

    public function render()
    {
        return view('livewire.stock-profitability-report');
    }
}
