<?php

namespace App\Livewire;

use App\Models\Equipment;
use App\Models\Transaction;
use App\Models\MaintenanceLog;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class StockProfitabilityReport extends Component
{
    public $reportData = [];

    public function mount()
    {
        $this->generateReport();
    }

    public function generateReport()
    {
        // 1. Receitas de Aluguel (Transações)
        // Busca transações de aluguel (assumindo que 'rental' é uma categoria ou que receitas de aluguel têm um 'customer_id' e tipo 'income')
        // *NOTA: Para precisão total, você precisaria de uma tabela 'rentals' mais detalhada.
        // Assumindo que todas as transações de 'income' vinculadas a um equipamento são receitas de aluguel:
        $rentalRevenues = Transaction::select('equipment_id', DB::raw('SUM(amount) as total_revenue'))
            ->whereNotNull('equipment_id')
            ->where('type', 'income')
            ->groupBy('equipment_id')
            ->get()
            ->keyBy('equipment_id');

        // 2. Custos de Manutenção
        $maintenanceCosts = MaintenanceLog::select('equipment_id', DB::raw('SUM(cost) as total_maintenance_cost'))
            ->groupBy('equipment_id')
            ->get()
            ->keyBy('equipment_id');

        // 3. Compilação do Relatório por Equipamento
        $equipments = Equipment::all();
        $this->reportData = [];

        foreach ($equipments as $equipment) {
            $revenue = $rentalRevenues->get($equipment->id)->total_revenue ?? 0;
            $maintenance = $maintenanceCosts->get($equipment->id)->total_maintenance_cost ?? 0;
            $initialCost = $equipment->initial_cost;

            // Custo total acumulado (Custo Inicial + Manutenção)
            $totalCost = $initialCost + $maintenance;

            // Lucro Bruto (Apenas comparando Receita vs Custo de Manutenção)
            $grossProfit = $revenue - $maintenance;

            // Lucro Líquido Real (Receita - Custo Total)
            $netProfit = $revenue - $totalCost;

            // % de Retorno sobre o Investimento (ROI)
            $roi = $initialCost > 0 ? ($netProfit / $initialCost) * 100 : 0;

            $this->reportData[] = [
                'name' => $equipment->name,
                'initial_cost' => $initialCost,
                'total_revenue' => $revenue,
                'total_maintenance_cost' => $maintenance,
                'net_profit' => $netProfit,
                'roi' => $roi,
            ];
        }
    }

    public function render()
    {
        return view('livewire.stock-profitability-report');
    }
}
