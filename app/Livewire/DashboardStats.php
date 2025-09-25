<?php

namespace App\Livewire;

use App\Models\Invoice;
use App\Models\Rental;
use App\Models\Equipment;
use App\Models\Transaction;
use Livewire\Component;
use Carbon\Carbon;

class DashboardStats extends Component
{
    public $activeRentals = 0;
    public $dailyRevenue = 0;
    public $weeklyRevenue = 0;
    public $monthlyRevenue = 0;
    public $yearlyRevenue = 0;
    public $topEquipment = [];

    // Dados para o gráfico
    public $chartLabels = [];
    public $chartIncome = [];
    public $chartExpenses = [];

    public function mount()
    {
        $this->fetchStats();
    }

    public function fetchStats()
    {
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $thisMonth = Carbon::now()->startOfMonth();

        // Aluguéis ativos
        $this->activeRentals = Rental::where('status', 'active')->count();

        // Faturamento diário
        $this->dailyRevenue = Invoice::where('status', 'paid')
            ->whereDate('created_at', $today)
            ->sum('total');

        // Faturamento semanal (de segunda a domingo)
        $this->weeklyRevenue = Invoice::where('status', 'paid')
            ->whereBetween('created_at', [$thisWeek, $thisWeek->copy()->endOfWeek(Carbon::SUNDAY)])
            ->sum('total');

        // Faturamento mensal e anual
        $this->monthlyRevenue = Invoice::where('status', 'paid')
            ->whereBetween('created_at', [$thisMonth, $thisMonth->copy()->endOfMonth()])
            ->sum('total');

        $this->yearlyRevenue = Invoice::where('status', 'paid')
            ->whereYear('created_at', now()->year)
            ->sum('total');

        // Top 5 equipamentos mais alugados
        $this->topEquipment = Equipment::select('equipment.name')
            ->selectRaw('count(rental_id) as total_rentals')
            ->join('equipment_rental', 'equipment.id', '=', 'equipment_rental.equipment_id')
            ->groupBy('equipment.name')
            ->orderByDesc('total_rentals')
            ->limit(5)
            ->get();
    }

    // Método para preparar os dados do gráfico
    public function getChartData()
    {
        $months = [];
        $incomeData = [];
        $expenseData = [];

        for ($i = 6; $i > 0; $i--) {
            $month = Carbon::now()->subMonths($i - 1);
            $months[] = $month->format('M');

            $incomeData[] = Invoice::where('status', 'paid')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('total');

            $expenseData[] = Transaction::where('type', 'expense')
                ->whereYear('date', $month->year)
                ->whereMonth('date', $month->month)
                ->sum('amount');
        }

        $this->chartLabels = $months;
        $this->chartIncome = $incomeData;
        $this->chartExpenses = $expenseData;
    }

    public function render()
    {
        $this->getChartData();

        return view('livewire.dashboard-stats');
    }
}
