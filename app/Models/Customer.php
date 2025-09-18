<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Customer extends Model
{
    use HasTenant;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'tenant_id'
    ];

    public function rentals(): HasMany
    {
        return $this->hasMany(Rental::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
