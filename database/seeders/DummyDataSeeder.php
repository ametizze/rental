<?php
// database/seeders/DummyDataSeeder.php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\Customer;
use App\Models\Equipment;
use App\Models\Rental;
use App\Models\Invoice;
use App\Models\InvoiceItem;
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
        $startDate = Carbon::create(2025, 6, 1);
        $endDate = Carbon::create(2025, 8, 31);

        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $numRentals = rand(1, 3);

            for ($i = 0; $i < $numRentals; $i++) {
                $customer = $customers->random();
                $rentalEquipment = $equipment->random(rand(1, 2));

                $rentalStartDate = $currentDate->copy()->addDays(rand(0, 5));
                $rentalEndDate = $rentalStartDate->copy()->addDays(rand(1, 7));

                $totalAmount = 0;
                foreach ($rentalEquipment as $item) {
                    $days = $rentalStartDate->diffInDays($rentalEndDate) + 1;
                    $totalAmount += $item->daily_rate * $days;
                }

                // Linha corrigida: Decodifica o JSON para um array
                $tenantSettings = json_decode($tenant->settings, true);
                $taxRate = $tenantSettings['tax_rate'];

                $taxAmount = $totalAmount * $taxRate;
                $grandTotal = $totalAmount + $taxAmount;

                // 1. Cria o Aluguel (Rental)
                $rental = Rental::create([
                    'uuid' => (string) Str::uuid(),
                    'tenant_id' => $tenant->id,
                    'customer_id' => $customer->id,
                    'start_date' => $rentalStartDate,
                    'end_date' => $rentalEndDate,
                    'total_amount' => $grandTotal,
                    'status' => 'completed',
                ]);
                $rental->equipment()->sync($rentalEquipment->pluck('id')->toArray());

                // 2. Cria a Fatura (Invoice)
                $invoice = Invoice::create([
                    'tenant_id' => $tenant->id,
                    'customer_id' => $customer->id,
                    'uuid' => Str::uuid(),
                    'bill_to_name' => $customer->name,
                    'bill_to_email' => $customer->email,
                    'bill_to_phone' => $customer->phone,
                    'tax_rate' => $taxRate,
                    'subtotal' => $totalAmount,
                    'tax_amount' => $taxAmount,
                    'total' => $grandTotal,
                    'status' => rand(0, 1) ? 'paid' : 'unpaid', // 50% de chance de estar pago
                    'due_date' => $rentalEndDate->copy()->addDays(7),
                ]);

                // 3. Cria os Itens da Fatura (InvoiceItems)
                foreach ($rentalEquipment as $item) {
                    $days = $rentalStartDate->diffInDays($rentalEndDate) + 1;
                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'description' => $item->name,
                        'quantity' => $days,
                        'rate' => $item->daily_rate,
                        'amount' => $item->daily_rate * $days,
                    ]);
                }
            }
            $currentDate->addDay();
        }
        echo "Dummy data generated for 3 months.\n";
    }
}
