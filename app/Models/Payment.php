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
     * Define the model events for cascading deletion.
     */
    protected static function booted()
    {
        parent::boot();

        // CRUCIAL: Delete the associated Transaction record when a Payment is deleted.
        static::deleting(function (Payment $payment) {
            Transaction::where('source_type', Payment::class)
                ->where('source_id', $payment->id)
                ->delete();
        });
    }

    /**
     * Relation: Payment belongs to an Invoice
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
