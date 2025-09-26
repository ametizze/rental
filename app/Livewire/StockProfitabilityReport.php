<?php

namespace App\Livewire;

use App\Models\Equipment;
use App\Models\Transaction;
use App\Models\MaintenanceLog; // Manter este import se você precisar de source_type
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class StockProfitabilityReport extends Component
{
    use WithPagination;

    public $reportData = [];

    public function mount()
    {
        $this->generateReport();
    }

    public function generateReport()
    {
        $tenantId = auth()->user()->tenant_id;
        $reportData = [];

        // 1. Unify all costs and revenues from the 'transactions' table
        $unifiedTransactions = Transaction::select('equipment_id', 'type', DB::raw('SUM(amount) as total_amount'))
            ->where('tenant_id', $tenantId)
            ->whereNotNull('equipment_id')
            ->groupBy('equipment_id', 'type')
            ->get()
            ->groupBy('equipment_id');

        // 2. Compile the report by iterating over all equipment
        $equipments = Equipment::all();

        foreach ($equipments as $equipment) {
            $equipmentTransactions = $unifiedTransactions->get($equipment->id) ?? collect();

            // Sum revenues and expenses for this equipment from the unified collection
            $totalRevenue = $equipmentTransactions->where('type', 'income')->sum('total_amount');
            $totalExpenses = $equipmentTransactions->where('type', 'expense')->sum('total_amount');
            $initialCost = $equipment->initial_cost;

            // Since initial cost is also an expense in transactions, we add it here
            $netProfit = $totalRevenue - $totalExpenses;

            $roi = $initialCost > 0 ? ($netProfit / $initialCost) * 100 : 0;

            $reportData[] = [
                'name' => $equipment->name,
                'initial_cost' => $initialCost,
                'total_revenue' => $totalRevenue,
                'total_maintenance_cost' => $totalExpenses - $initialCost, // Maintenance is total expenses minus initial cost
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
