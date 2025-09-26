<?php

namespace App\Livewire;

use App\Models\Invoice;
use App\Models\Rental;
use App\Models\Equipment;
use App\Models\Transaction;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardStats extends Component
{
    // KPIs (Calculated as Net Flow: Income - Expense)
    public $dailyNetFlow = 0;
    public $weeklyNetFlow = 0;
    public $monthlyNetFlow = 0;
    public $yearlyNetFlow = 0;

    // Management Indicators
    public $activeRentals = 0;
    public $balanceReceivable = 0;
    public $pendingInvoicesWeekly = 0;
    public $overdueInvoicesCount = 0;

    // Chart Data
    public $topEquipment = [];
    public $chartLabels = [];
    public $chartIncome = [];
    public $chartExpenses = [];

    public function mount()
    {
        $this->fetchStats();
    }

    /**
     * Fetches all key statistics and KPIs directly from the database (Net Flow logic).
     */
    public function fetchStats()
    {
        $tenantId = auth()->user()->tenant_id;
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $thisMonth = Carbon::now()->startOfMonth();
        $now = now();

        // Base queries for completed transactions (used for Net Flow)
        $completedTransactions = Transaction::where('tenant_id', $tenantId)->where('status', 'received');
        $incomeBase = (clone $completedTransactions)->where('type', 'income');
        $expenseBase = (clone $completedTransactions)->where('type', 'expense');

        // Helper function to calculate Net Flow (Income - Expense) from raw data
        $calculateNetFlow = function ($incomeSum, $expenseSum) {
            return $incomeSum - $expenseSum;
        };

        // --- 1. NET FLOW CALCULATION ---

        // DAILY NET FLOW
        $dailyIncome = (clone $incomeBase)->whereDate('date', $today)->sum('amount');
        $dailyExpense = (clone $expenseBase)->whereDate('date', $today)->sum('amount');
        $this->dailyNetFlow = $calculateNetFlow($dailyIncome, $dailyExpense);

        // WEEKLY NET FLOW
        $weeklyIncome = (clone $incomeBase)->whereBetween('date', [$thisWeek, $thisWeek->copy()->endOfWeek(Carbon::SUNDAY)])->sum('amount');
        $weeklyExpense = (clone $expenseBase)->whereBetween('date', [$thisWeek, $thisWeek->copy()->endOfWeek(Carbon::SUNDAY)])->sum('amount');
        $this->weeklyNetFlow = $calculateNetFlow($weeklyIncome, $weeklyExpense);

        // MONTHLY NET FLOW
        $monthlyIncome = (clone $incomeBase)->whereBetween('date', [$thisMonth, $thisMonth->copy()->endOfMonth()])->sum('amount');
        $monthlyExpense = (clone $expenseBase)->whereBetween('date', [$thisMonth, $thisMonth->copy()->endOfMonth()])->sum('amount');
        $this->monthlyNetFlow = $calculateNetFlow($monthlyIncome, $monthlyExpense);

        // YEARLY NET FLOW
        $yearlyIncome = (clone $incomeBase)->whereYear('date', $now->year)->sum('amount');
        $yearlyExpense = (clone $expenseBase)->whereYear('date', $now->year)->sum('amount');
        $this->yearlyNetFlow = $calculateNetFlow($yearlyIncome, $yearlyExpense);

        // --- 2. MANAGEMENT INDICATORS ---
        $unpaidInvoicesBase = Invoice::where('tenant_id', $tenantId)->whereIn('status', ['unpaid', 'partially_paid']);

        $this->activeRentals = Rental::where('tenant_id', $tenantId)->where('status', 'active')->count();

        // Total Accounts Receivable (Balance Due)
        $this->balanceReceivable = $unpaidInvoicesBase->sum(DB::raw('total - paid_amount'));

        // Invoices Due THIS WEEK (Pending/Overdue)
        $this->pendingInvoicesWeekly = (clone $unpaidInvoicesBase)
            ->whereBetween('due_date', [$thisWeek, $thisWeek->copy()->endOfWeek(Carbon::SUNDAY)])
            ->count();

        // Total Overdue Invoices Count
        $this->overdueInvoicesCount = (clone $unpaidInvoicesBase)
            ->whereDate('due_date', '<', $today)
            ->count();

        // --- 3. TOP 5 EQUIPMENT (Logic remains stable) ---
        $this->topEquipment = Equipment::withoutGlobalScope('tenant')
            ->select('equipment.name')
            ->selectRaw('count(rental_id) as total_rentals')
            ->join('equipment_rental', 'equipment.id', '=', 'equipment_rental.equipment_id')
            ->join('rentals', 'rentals.id', '=', 'equipment_rental.rental_id')
            ->where('rentals.tenant_id', $tenantId)
            ->groupBy('equipment.name')
            ->orderByDesc('total_rentals')
            ->limit(5)
            ->get();
    }

    /**
     * Prepares historical data for the bar chart from the unified transactions table.
     */
    public function getChartData()
    {
        $tenantId = auth()->user()->tenant_id;
        $months = [];
        $incomeData = [];
        $expenseData = [];

        for ($i = 6; $i > 0; $i--) {
            $month = Carbon::now()->subMonths($i - 1);
            $months[] = $month->format('M');

            $incomeData[] = Transaction::where('tenant_id', $tenantId)
                ->where('type', 'income')
                ->where('status', 'received')
                ->whereYear('date', $month->year)
                ->whereMonth('date', $month->month)
                ->sum('amount');

            $expenseData[] = Transaction::where('tenant_id', $tenantId)
                ->where('type', 'expense')
                ->where('status', 'received')
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
