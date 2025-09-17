<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = [
        'uuid',
        'customer_id',
        'bill_to_name',
        'bill_to_email',
        'bill_to_phone',
        'bill_to_addr',
        'tax_rate',
        'subtotal',
        'tax_amount',
        'total',
        'status',
        'due_date',
        'notes',
        'photos',
        'tenant_id'
    ];

    protected $casts = [
        'due_date' => 'date',
        'tax_rate' => 'float',
        'subtotal' => 'float',
        'tax_amount' => 'float',
        'total' => 'float',
        'photos' => 'array',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
