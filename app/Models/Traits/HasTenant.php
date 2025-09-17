<?php
// app/Models/Traits/HasTenant.php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasTenant
{
    protected static function booted(): void
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            // If the user is authenticated and is not a superadmin, apply the tenant filter.
            if (auth()->check() && auth()->user()->role !== 'superadmin') {
                $builder->where('tenant_id', auth()->user()->tenant_id);
            }
        });

        // Event to ensure the tenant_id is automatically filled when creating a new record.
        static::creating(function ($model) {
            if (auth()->check() && auth()->user()->role !== 'superadmin') {
                $model->tenant_id = auth()->user()->tenant_id;
            }
        });
    }
}
