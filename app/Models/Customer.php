<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'code',
        'name',
        'email',
        'phone',
        'alt_phone',
        'notes'
    ];
}
