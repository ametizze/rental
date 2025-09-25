<?php
// database/seeders/DummyDataSeeder.php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\Customer;
use App\Models\Equipment;
use App\Models\Rental;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::first();

        // --- Clientes de Demonstração ---
        $customersData = [
            ['name' => 'GreenScape Landscaping', 'email' => 'contact@greenscape.com', 'phone' => '+1 (555) 100-0100'],
            ['name' => 'City Renovations', 'email' => 'info@cityrenovations.com', 'phone' => '+1 (555) 200-0200'],
            ['name' => 'Precision Flooring LLC', 'email' => 'sales@precisionfloor.com', 'phone' => '+1 (555) 300-0300'],
            ['name' => 'Urban Developers Inc.', 'email' => 'contact@urbandevelopers.com', 'phone' => '+1 (555) 400-0400'],
            ['name' => 'Southside Builders', 'email' => 'hello@southsidebuilders.com', 'phone' => '+1 (555) 500-0500'],
        ];

        foreach ($customersData as $data) {
            Customer::create(array_merge($data, ['tenant_id' => $tenant->id]));
        }
        $customers = Customer::where('tenant_id', $tenant->id)->get();
        echo "Customers seeded for {$tenant->name}\n";

        // --- Equipamentos de Demonstração ---
        $equipmentData = [
            ['name' => 'Electric Tile Saw', 'category' => 'Tile', 'serial' => 'TS-8842', 'daily_rate' => 65.00],
            ['name' => 'Drywall Lift', 'category' => 'Drywall', 'serial' => 'DL-3391', 'daily_rate' => 40.00],
            ['name' => 'Power Trowel 36"', 'category' => 'Concrete', 'serial' => 'PT-7620', 'daily_rate' => 85.00],
            ['name' => 'Floor Sander', 'category' => 'Flooring', 'serial' => 'FS-1100', 'daily_rate' => 95.00],
            ['name' => 'Concrete Mixer', 'category' => 'Concrete', 'serial' => 'CM-2500', 'daily_rate' => 75.00],
            ['name' => 'Pressure Washer', 'category' => 'Cleaning', 'serial' => 'PW-4000', 'daily_rate' => 50.00],
        ];

        foreach ($equipmentData as $data) {
            Equipment::create(array_merge($data, ['qr_uuid' => Str::uuid(), 'tenant_id' => $tenant->id]));
        }
        $equipment = Equipment::where('tenant_id', $tenant->id)->get();
        echo "Equipment seeded for {$tenant->name}\n";

        // --- Geração de Dados Fictícios de 3 Meses ---
        $startDate = Carbon::now()->subMonths(3)->startOfMonth();
        $endDate = Carbon::now();

        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $numRentals = rand(1, 3);

            for ($i = 0; $i < $numRentals; $i++) {
                $customer = $customers->random();
                $rentalEquipment = $equipment->random(rand(1, 2));

                $rentalStartDate = $currentDate->copy()->addDays(rand(0, 5));
                $rentalEndDate = $rentalStartDate->copy()->addDays(rand(1, 7));

                $totalAmountWithoutTax = 0;
                $days = $rentalStartDate->diffInDays($rentalEndDate) + 1;

                foreach ($rentalEquipment as $item) {
                    $totalAmountWithoutTax += $item->daily_rate * $days;
                }

                $tenantSettings = json_decode($tenant->settings, true);
                $taxRate = $tenantSettings['tax_rate'] ?? 0;
                $taxAmount = $totalAmountWithoutTax * $taxRate;
                $grandTotal = $totalAmountWithoutTax + $taxAmount;

                // 1. Cria o Aluguel (Rental)
                $rental = Rental::create([
                    // ... (Campos do Rental)
                ]);
                $rental->equipment()->sync($rentalEquipment->pluck('id')->toArray());

                // 2. Cria a Fatura (Invoice)
                $invoice = Invoice::create([
                    // ... (Campos do Invoice)
                ]);

                // 3. Cria os Itens da Fatura e as Transações de Receita
                $isPaid = rand(0, 1);

                foreach ($rentalEquipment as $item) {
                    $itemRevenue = $item->daily_rate * $days;

                    // Cria o Item da Fatura
                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'description' => $item->name . ' (Rental)',
                        'quantity' => $days,
                        'rate' => $item->daily_rate,
                        'amount' => $itemRevenue,
                    ]);

                    // **Cria a Transação de Receita para o Equipamento**
                    if ($isPaid) {
                        Transaction::create([
                            'tenant_id' => $tenant->id,
                            'type' => 'income',
                            'amount' => $itemRevenue, // A receita sem imposto é vinculada ao equipamento
                            'description' => 'Rental Revenue: ' . $item->name,
                            'source_id' => $invoice->id,
                            'source_type' => Invoice::class,
                            'equipment_id' => $item->id, // <--- O ELO PERDIDO
                            'date' => $rentalEndDate,
                            'status' => 'received'
                        ]);
                    }
                }
            }
            $currentDate->addDay();
        }
        echo "Dummy data generated for 3 months.\n";
    }
}
