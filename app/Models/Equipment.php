<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Equipment extends Model
{
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
}
