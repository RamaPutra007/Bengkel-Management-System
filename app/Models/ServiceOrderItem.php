<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\ServiceOrder;
use App\Models\Service;
use App\Models\Sparepart;

class ServiceOrderItem extends Model
{
    protected $fillable = ['service_order_id', 'type', 'service_id', 'sparepart_id', 'item_name', 'quantity', 'price', 'subtotal'];

    public function serviceOrder()
    {
        return $this->belongsTo(ServiceOrder::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function sparepart()
    {
        return $this->belongsTo(Sparepart::class);
    }
}
