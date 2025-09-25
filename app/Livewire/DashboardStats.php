<?php

namespace App\Livewire;

use App\Models\Invoice;
use App\Models\Rental;
use App\Models\Equipment;
use App\Models\Transaction;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; // Necessário para consultas avançadas

class DashboardStats extends Component
{
    public $activeRentals = 0;
    public $dailyRevenue = 0;
    public $weeklyRevenue = 0;
    public $monthlyRevenue = 0;
    public $yearlyRevenue = 0;
    public $topEquipment = [];

    // NOVOS INDICADORES FINANCEIROS
    public $balanceReceivable = 0; // Contas a Receber
    public $balancePayable = 0;    // Contas a Pagar (Despesas Pendentes)

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
        $tenantId = auth()->user()->tenant_id;

        // --- KPIS DE RECEITA (Essas consultas não precisam de correção, pois o Global Scope funciona bem) ---
        $this->activeRentals = Rental::where('tenant_id', $tenantId)->where('status', 'active')->count();
        $this->dailyRevenue = Invoice::where('tenant_id', $tenantId)->where('status', 'paid')->whereDate('created_at', $today)->sum('total');
        $this->weeklyRevenue = Invoice::where('tenant_id', $tenantId)->where('status', 'paid')->whereBetween('created_at', [$thisWeek, $thisWeek->copy()->endOfWeek(Carbon::SUNDAY)])->sum('total');
        $this->monthlyRevenue = Invoice::where('tenant_id', $tenantId)->where('status', 'paid')->whereBetween('created_at', [$thisMonth, $thisMonth->copy()->endOfMonth()])->sum('total');
        $this->yearlyRevenue = Invoice::where('tenant_id', $tenantId)->where('status', 'paid')->whereYear('created_at', now()->year)->sum('total');

        // Contas a Receber e a Pagar (o código permanece o mesmo)
        $unpaidInvoices = Invoice::where('tenant_id', $tenantId)
            ->whereIn('status', ['unpaid', 'partially_paid'])
            ->get();
        $this->balanceReceivable = $unpaidInvoices->sum(function ($invoice) {
            return $invoice->total - $invoice->paid_amount;
        });

        $this->balancePayable = Transaction::where('tenant_id', $tenantId)
            ->where('type', 'expense')
            ->where('status', 'pending')
            ->sum('amount');


        // --- CORREÇÃO DO TOP 5 EQUIPAMENTOS ---
        // Desativamos o Global Scope do Equipment para evitar a ambiguidade de 'tenant_id'
        $this->topEquipment = Equipment::withoutGlobalScope('tenant')
            ->select('equipment.name')
            ->selectRaw('count(rental_id) as total_rentals')
            ->join('equipment_rental', 'equipment.id', '=', 'equipment_rental.equipment_id')
            ->join('rentals', 'rentals.id', '=', 'equipment_rental.rental_id')
            ->where('rentals.tenant_id', $tenantId) // Filtro explícito (CORRETO)
            ->groupBy('equipment.name')
            ->orderByDesc('total_rentals')
            ->limit(5)
            ->get();
    }

    public function getChartData()
    {
        $tenantId = auth()->user()->tenant_id;
        $months = [];
        $incomeData = [];
        $expenseData = [];

        // Prepara dados para os últimos 6 meses
        for ($i = 6; $i > 0; $i--) {
            $month = Carbon::now()->subMonths($i - 1);
            $months[] = $month->format('M');

            $incomeData[] = Invoice::where('tenant_id', $tenantId)
                ->where('status', 'paid')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('total');

            $expenseData[] = Transaction::where('tenant_id', $tenantId)
                ->where('type', 'expense')
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
