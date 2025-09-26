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
        'status', // Agora pode ser atualizado para 'return', 'archived', etc.
    ];

    protected $casts = [
        'amount' => 'float',
        'date' => 'date',
        'due_date' => 'date',
    ];

    // ... (Método booted permanece o mesmo)

    // Relationships (permanecem os mesmos)
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

    /**
     * Custom accessor to calculate status for reporting.
     * This defines the hierarchy of statuses (Overdue > Pending > Paid).
     */
    public function getCalculatedStatusAttribute(): string
    {
        // Statuses definitivos que não mudam (paid/received, archived, return)
        if (in_array($this->status, ['received', 'paid', 'archived', 'return'])) {
            return $this->status;
        }

        // Se for uma transação de Receita (Income) e tiver vencimento
        if ($this->type === 'income' && $this->due_date) {
            // Verifica se está atrasada
            if ($this->status === 'pending' && $this->due_date->isPast()) {
                return 'overdue';
            }
        }

        // Retorna o status original para 'pending' ou 'scheduled'
        return $this->status;
    }
}
