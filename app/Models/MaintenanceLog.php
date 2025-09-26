<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceLog extends Model
{
    use HasTenant;

    protected static function booted()
    {
        parent::boot();

        // CRUCIAL: This event is triggered before the deletion of the MaintenanceLog.
        // It ensures that the corresponding transaction is deleted.
        static::deleting(function (MaintenanceLog $log) {
            Transaction::where('source_type', MaintenanceLog::class)
                ->where('source_id', $log->id)
                ->delete();
        });
    }

    protected $fillable = [
        'tenant_id',
        'equipment_id',
        'cost',
        'description',
        'date',
    ];

    protected $casts = [
        'cost' => 'float',
        'date' => 'date',
    ];

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }
}
