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
        Tenant::firstOrCreate(['id' => 1], [
            'name' => 'EasyRental',
            'settings' => [
                'currency' => 'USD',
                'timezone' => 'America/New_York',
                'language' => 'en',
                'date_format' => 'Y-m-d',
                'time_format' => 'H:i:s',
            ]
        ]);
    }
}
