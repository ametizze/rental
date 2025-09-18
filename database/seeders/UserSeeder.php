<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenant = Tenant::first();

        // Superadmin (sem tenant)
        User::create([
            'name' => 'Superadmin',
            'email' => 'superadmin@multisystem.cloud',
            'password' => Hash::make('admin'),
            'role' => 'superadmin',
            'tenant_id' => null
        ]);

        // Admin do tenant de demonstração
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@multisystem.cloud',
            'password' => Hash::make('admin'),
            'role' => 'admin',
            'tenant_id' => $tenant->id
        ]);
    }
}
