<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssetPhoto extends Model
{
    use SoftDeletes;

    protected $fillable = ['tenant_id', 'asset_id', 'path', 'caption', 'taken_at'];

    protected $casts = ['taken_at' => 'datetime'];
}
