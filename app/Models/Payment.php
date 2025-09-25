<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasTenant; // Ensure your tenant will be set automatically

    protected $fillable = [
        'invoice_id',
        'amount',
        'payment_date',
        'notes',
        'tenant_id',
    ];

    protected $casts = [
        'payment_date' => 'date',
    ];

    /**
     * Relation: Payment belongs to an Invoice
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
