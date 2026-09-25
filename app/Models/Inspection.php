<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\ServiceOrder;

class Inspection extends Model
{
    protected $guarded = [];

    public function serviceOrder()
    {
        return $this->belongsTo(ServiceOrder::class);
    }
}
