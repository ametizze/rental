<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'category_id',
        'code',
        'make',
        'model',
        'serial_number',
        'year',
        'status',
        'price_per_day_cents',
        'description'
    ];

    // Simple relationship helpers
    public function category()
    {
        return $this->belongsTo(AssetCategory::class, 'category_id');
    }
    public function photos()
    {
        return $this->hasMany(AssetPhoto::class);
    }

    // Convenience money accessors (optional DX sugar)
    public function getPricePerDayAttribute(): ?float
    {
        return is_null($this->price_per_day_cents) ? null : $this->price_per_day_cents / 100;
    }
    public function setPricePerDayAttribute($value): void
    {
        $this->price_per_day_cents = is_null($value) ? null : (int) round(((float) $value) * 100);
    }
}
