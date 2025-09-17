<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Tenant::create([
            'name' => 'EasyRental Demo Company',
            'domain' => 'demo',
            'email' => 'rental@multisystem.cloud',
            'phone' => '+1 (555) 123-4567',
            'address' => '123 Main Street',
            'city' => 'Anytown',
            'state' => 'CT',
            'zipcode' => '06800',
            'responsible_person' => 'Jane Doe',
            'is_active' => true,
            'settings' => json_encode(['tax_rate' => 0.0635, 'currency' => 'USD'])
        ]);
    }
}
