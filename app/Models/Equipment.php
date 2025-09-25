<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
        'initial_cost',
        'purchase_date',
        'qr_uuid',
        'tenant_id'
    ];

    protected $casts = [
        'daily_rate' => 'float',
        'initial_cost' => 'float',
        'purchase_date' => 'date',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function maintenanceLogs()
    {
        return $this->hasMany(MaintenanceLog::class);
    }

    public function rentals(): BelongsToMany
    {
        return $this->belongsToMany(Rental::class, 'equipment_rental');
    }
}
