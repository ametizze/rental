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

    /**
     * Define the model events for cascading deletion.
     */
    protected static function booted()
    {
        parent::boot();

        // CRUCIAL: Cascade deletion when Equipment is deleted.
        static::deleting(function (Equipment $equipment) {
            // 1. Delete the initial CAPEX Transaction (where Equipment itself is the source)
            Transaction::where('source_type', Equipment::class)
                ->where('source_id', $equipment->id)
                ->delete();

            // 2. Delete all Maintenance Logs (The MaintenanceLog event will handle its Transaction)
            $equipment->maintenanceLogs()->delete();
        });
    }

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
