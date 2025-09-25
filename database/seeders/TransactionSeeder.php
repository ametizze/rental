<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Models\Transaction;
use App\Models\Invoice;
use Carbon\Carbon;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenant = Tenant::first();

        // Se nenhum tenant foi encontrado, não continue.
        if (!$tenant) {
            return;
        }

        $startDate = Carbon::create(2025, 7, 1);
        $endDate = Carbon::create(2025, 9, 30);

        while ($startDate->lte($endDate)) {
            // Gerar despesas aleatórias para o mês
            $numExpenses = rand(3, 7);
            for ($i = 0; $i < $numExpenses; $i++) {
                $description = ['Maintenance', 'Office Supplies', 'Fuel', 'Rent', 'Utilities'][array_rand(['Maintenance', 'Office Supplies', 'Fuel', 'Rent', 'Utilities'])];
                Transaction::create([
                    'tenant_id' => $tenant->id,
                    'type' => 'expense',
                    'amount' => rand(50, 500),
                    'description' => $description . ' ' . $startDate->format('M Y'),
                    'date' => $startDate->copy()->addDays(rand(0, 27)),
                ]);
            }

            // Gerar receitas a partir de faturas pagas
            $invoices = Invoice::where('tenant_id', $tenant->id)
                ->where('status', 'paid')
                ->whereMonth('created_at', $startDate->month)
                ->get();

            foreach ($invoices as $invoice) {
                Transaction::create([
                    'tenant_id' => $tenant->id,
                    'type' => 'income',
                    'amount' => $invoice->total,
                    'description' => 'Payment for Invoice #' . $invoice->id,
                    'source_id' => $invoice->id,
                    'source_type' => Invoice::class,
                    'date' => $invoice->created_at,
                ]);
            }

            $startDate->addMonth();
        }
    }
}
