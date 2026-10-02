<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ServiceOrder;

class Invoice extends Model
{
    use SoftDeletes;
    protected $fillable = ['invoice_number', 'service_order_id', 'subtotal', 'tax', 'discount', 'grand_total', 'payment_method', 'payment_status', 'paid_at', 'notes'];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function serviceOrder()
    {
        return $this->belongsTo(ServiceOrder::class);
    }
}
