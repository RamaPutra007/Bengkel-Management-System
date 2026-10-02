<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\ServiceOrder;

class Inspection extends Model
{
    protected $fillable = ['service_order_id', 'customer_complaint', 'mechanic_notes', 'recommendations'];

    public function serviceOrder()
    {
        return $this->belongsTo(ServiceOrder::class);
    }
}
