<?php
// app/Models/Transaction.php
namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Transaction extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id',
        'type',
        'amount',
        'description',
        'source_id',
        'source_type',
        'date',
        'category_id',
        'customer_id',
        'equipment_id',
        'due_date',
        'status',
    ];

    protected $casts = [
        'amount' => 'float',
        'date' => 'date',
        'due_date' => 'date',
    ];

    // Relationships
    public function category(): BelongsTo
    {
        return $this->belongsTo(TransactionCategory::class, 'category_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    // Custom accessor to calculate status for reporting
    public function getCalculatedStatusAttribute(): string
    {
        // If the status is definitively set (e.g., received), return it.
        if ($this->status === 'received' || $this->status === 'paid') {
            return 'received';
        }

        // Apply overdue logic only to income (receivables) with a due date
        if ($this->type === 'income' && $this->status === 'pending' && $this->due_date && $this->due_date->isPast()) {
            return 'overdue';
        }

        return $this->status; // Returns 'pending' or 'scheduled'
    }
}
