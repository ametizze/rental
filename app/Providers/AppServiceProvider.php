<?php

namespace App\Providers;

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
    }
}
