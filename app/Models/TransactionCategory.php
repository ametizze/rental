<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;

class TransactionCategory extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'type', // income or expense
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'category_id');
    }
}
