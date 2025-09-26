<?php

namespace App\Models;

// app/Models/StockItem.php

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;

class StockItem extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'unit_price',
        'quantity',
        'unit',
        'reference_code',
        'photo_path',
    ];

    protected $casts = [
        'unit_price' => 'float',
        'quantity' => 'integer',
    ];
}
