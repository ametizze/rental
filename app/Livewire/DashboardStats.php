<?php
// app/Livewire/DashboardStats.php

namespace App\Livewire;

use App\Models\Invoice;
use App\Models\Rental;
use App\Models\Equipment;
use Livewire\Component;

class DashboardStats extends Component
{
    public $activeRentals = 0;
    public $monthlyRevenue = 0;
    public $yearlyRevenue = 0;
    public $topEquipment = [];

    public function mount()
    {
        $this->fetchStats();
    }

    public function fetchStats()
    {
        // 1. Aluguéis ativos
        $this->activeRentals = Rental::where('status', 'active')->count();

        // 2. Faturamento mensal
        $this->monthlyRevenue = Invoice::where('status', 'paid')
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('total');

        // 3. Faturamento anual
        $this->yearlyRevenue = Invoice::where('status', 'paid')
            ->whereYear('created_at', now()->year)
            ->sum('total');

        // 4. Top 5 equipamentos mais alugados
        // Essa consulta requer a tabela pivot, então usamos o DB
        $this->topEquipment = Equipment::select('equipment.name')
            ->selectRaw('count(rental_id) as total_rentals')
            ->join('equipment_rental', 'equipment.id', '=', 'equipment_rental.equipment_id')
            ->groupBy('equipment.name')
            ->orderByDesc('total_rentals')
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboard-stats');
    }
}
