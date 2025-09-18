<?php
// app/Models/Rental.php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Rental extends Model
{
    use HasFactory, HasTenant;

    protected $fillable = [
        'customer_id',
        'start_date',
        'end_date',
        'total_amount',
        'status',
        'start_photos',
        'end_photos',
        'tenant_id'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'start_photos' => 'array',
        'end_photos' => 'array',
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($rental) {
            $rental->uuid = (string) Str::uuid();
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function equipment(): BelongsToMany
    {
        return $this->belongsToMany(Equipment::class, 'equipment_rental');
    }
}
