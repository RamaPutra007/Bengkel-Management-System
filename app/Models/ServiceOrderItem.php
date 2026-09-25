<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\ServiceOrder;
use App\Models\Service;
use App\Models\Sparepart;

class ServiceOrderItem extends Model
{
    protected $guarded = [];

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
