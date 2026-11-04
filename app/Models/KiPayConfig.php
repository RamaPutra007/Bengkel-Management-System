<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KiPayConfig extends Model
{
    protected $table = 'kipay_configs';

    protected $fillable = [
        'api_key',
        'webhook_secret',
        'mode',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}

