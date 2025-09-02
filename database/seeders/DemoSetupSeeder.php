<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoSetupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Wrap in a transaction to keep the DB consistent if something fails
        DB::transaction(function () {
            // 1) Create or find tenant by slug (avoid forcing primary key)
            $tenant = Tenant::firstOrCreate(
                ['slug' => 'easy-rental'],
                [
                    'name' => 'EasyRental',
                    'settings' => [
                        'currency' => 'USD',
                        'timezone' => 'America/New_York',
                        'language' => 'en',
                        'date_format' => 'Y-m-d',
                        'time_format' => 'H:i:s',
                    ],
                ]
            );

            // 2) Create or find admin user for the tenant
            // Note: firstOrCreate will not overwrite an existing user's password.
            $user = User::firstOrCreate(
                ['email' => 'owner@demo.test'],
                [
                    'name' => 'Demo Owner',
                    // Weak demo password — change in production
                    'password' => Hash::make('password'),
                    // optional convenience field on users table
                    'tenant_id' => $tenant->id,
                ]
            );

            // 3) Link user to tenant (pivot). Prefer relationship if available.
            $pivotData = [
                'is_default' => true,
                'role' => 'admin',
                'updated_at' => now(),
                'created_at' => now(),
            ];

            if (method_exists($user, 'tenants')) {
                // syncWithoutDetaching preserves other tenant links and updates pivot data
                $user->tenants()->syncWithoutDetaching([$tenant->id => $pivotData]);
            } else {
                DB::table('user_tenants')->updateOrInsert(
                    ['user_id' => $user->id, 'tenant_id' => $tenant->id],
                    $pivotData
                );
            }

            // 4) (Optional) Make user a platform superadmin
            // Uncomment if your application uses a platform_admins table and you want this demo user to be a platform admin.
            // DB::table('platform_admins')->updateOrInsert(
            //     ['user_id' => $user->id],
            //     ['granted_at' => now()]
            // );
        });
    }
}
