<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Create app('currentTenant') if it doesn't exist
        if (Schema::hasTable('tenants')) {
            app()->singleton('currentTenant', function () {
                return auth()->check() ? Tenant::find(auth()->user()->tenant_id) : null;
            });
        }

        // Gates
        // Define Gate 'manage-tenants'
        Gate::define('manage-tenants', function ($user) {
            return $user->role === 'superadmin';
        });

        // Define Gate 'manage-users'
        Gate::define('manage-users', function ($user) {
            return in_array($user->role, ['superadmin', 'admin']);
        });
    }
}
