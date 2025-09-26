<?php

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
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the single demo tenant created in TenantSeeder
        $tenant = Tenant::first();

        // --- 1. Seed Demo Customers ---
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

        // --- 2. Seed Demo Equipment (with initial cost for profit tracking) ---
        $equipmentData = [
            ['name' => 'Electric Tile Saw', 'category' => 'Tile', 'serial' => 'TS-8842', 'daily_rate' => 65.00, 'initial_cost' => 850.00],
            ['name' => 'Drywall Lift', 'category' => 'Drywall', 'serial' => 'DL-3391', 'daily_rate' => 40.00, 'initial_cost' => 500.00],
            ['name' => 'Power Trowel 36"', 'category' => 'Concrete', 'serial' => 'PT-7620', 'daily_rate' => 85.00, 'initial_cost' => 1200.00],
            ['name' => 'Floor Sander', 'category' => 'Flooring', 'serial' => 'FS-1100', 'daily_rate' => 95.00, 'initial_cost' => 3000.00],
            ['name' => 'Pressure Washer', 'category' => 'Cleaning', 'serial' => 'PW-4000', 'daily_rate' => 50.00, 'initial_cost' => 600.00],
        ];

        foreach ($equipmentData as $data) {
            Equipment::create(array_merge($data, [
                'qr_uuid' => Str::uuid(),
                'tenant_id' => $tenant->id,
                'purchase_date' => Carbon::now()->subMonths(7)->format('Y-m-d')
            ]));
        }
        $equipment = Equipment::where('tenant_id', $tenant->id)->get();
        echo "Equipment seeded for {$tenant->name}\n";

        // --- 3. Generate 3 Months of Rentals, Invoices, and Transactions ---
        $startDate = Carbon::now()->subMonths(3)->startOfMonth();
        $endDate = Carbon::now();

        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $numRentals = rand(1, 3);
            $taxRate = json_decode($tenant->settings, true)['tax_rate'] ?? 0.0;

            for ($i = 0; $i < $numRentals; $i++) {
                $customer = $customers->random();
                // Ensure unique equipment selection for the rental
                $rentalEquipment = $equipment->random(rand(1, min(count($equipment), 2)));

                $rentalStartDate = $currentDate->copy()->addDays(rand(0, 5));
                $rentalEndDate = $rentalStartDate->copy()->addDays(rand(1, 7));

                $totalAmountWithoutTax = 0;
                $days = $rentalStartDate->diffInDays($rentalEndDate) + 1;

                foreach ($rentalEquipment as $item) {
                    $totalAmountWithoutTax += $item->daily_rate * $days;
                }

                $taxAmount = $totalAmountWithoutTax * $taxRate;
                $grandTotal = $totalAmountWithoutTax + $taxAmount;
                $isFullyPaid = rand(0, 1);

                // --- 3a. Create Rental (The core activity) ---
                $rental = Rental::create([
                    'tenant_id' => $tenant->id,
                    'customer_id' => $customer->id,
                    'start_date' => $rentalStartDate,
                    'end_date' => $rentalEndDate,
                    'total_amount' => $grandTotal,
                    'status' => 'completed',
                    'uuid' => Str::uuid(),
                ]);
                $rental->equipment()->sync($rentalEquipment->pluck('id')->toArray());

                // --- 3b. Create Invoice (The financial document) ---
                $invoice = Invoice::create([
                    'uuid' => Str::uuid(),
                    'tenant_id' => $tenant->id,
                    'customer_id' => $customer->id,
                    'rental_id' => $rental->id, // The crucial link to the rental
                    'bill_to_name' => $customer->name,
                    'bill_to_email' => $customer->email,
                    'bill_to_phone' => $customer->phone,
                    'tax_rate' => $taxRate,
                    'subtotal' => $totalAmountWithoutTax,
                    'tax_amount' => $taxAmount,
                    'total' => $grandTotal,
                    'paid_amount' => $isFullyPaid ? $grandTotal : 0, // Set paid amount for status
                    'status' => $isFullyPaid ? 'paid' : 'unpaid',
                    'due_date' => $rentalEndDate->copy()->addDays(7),
                ]);

                // --- 3c. Create Invoice Items and Income Transactions ---
                foreach ($rentalEquipment as $item) {
                    $itemRevenue = $item->daily_rate * $days;

                    // Create Invoice Item
                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'description' => $item->name . ' (Rental)',
                        'quantity' => $days,
                        'rate' => $item->daily_rate,
                        'amount' => $itemRevenue,
                    ]);

                    // Create Income Transaction (only if paid, for Dashboard accuracy)
                    if ($isFullyPaid) {
                        Transaction::create([
                            'tenant_id' => $tenant->id,
                            'type' => 'income',
                            'amount' => $itemRevenue,
                            'description' => 'Rental Revenue: ' . $item->name,
                            'source_id' => $invoice->id,
                            'source_type' => Invoice::class,
                            'equipment_id' => $item->id, // The link for Profit Report
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
