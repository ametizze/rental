<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceLog extends Model
{
    use HasTenant;

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
