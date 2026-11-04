<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ServiceOrder;

class Invoice extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'invoice_number',
        'service_order_id',
        'subtotal',
        'tax',
        'discount',
        'grand_total',
        'payment_method',
        'payment_status',
        'paid_at',
        'notes',
        'kipay_trx_id',
        'kipay_qr_url',
        'kipay_qr_payload',
        'kipay_amount',
        'kipay_fee',
        'kipay_net_amount',
        'kipay_expired_at',
        'kipay_provider',
        'kipay_delivery_id',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'kipay_expired_at' => 'datetime',
    ];

    public function serviceOrder()
    {
        return $this->belongsTo(ServiceOrder::class);
    }
}
