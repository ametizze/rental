<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Equipment extends Model
{
    use HasTenant;

    protected $fillable = [
        'name',
        'category',
        'serial',
        'photo',
        'daily_rate',
        'status',
        'qr_uuid',
        'tenant_id'
    ];

    protected $casts = [
        'daily_rate' => 'float',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function rentals(): BelongsToMany
    {
        return $this->belongsToMany(Rental::class, 'equipment_rental');
    }
}
