<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ServiceOrder;

class Invoice extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function serviceOrder()
    {
        return $this->belongsTo(ServiceOrder::class);
    }
}
