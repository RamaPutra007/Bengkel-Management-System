<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Customer;
use App\Models\Vehicle;
use App\Models\Mechanic;

class ServiceOrder extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    public function inspection()
    {
        return $this->hasOne(Inspection::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function mechanic()
    {
        return $this->belongsTo(Mechanic::class);
    }

    public function items()
    {
        return $this->hasMany(ServiceOrderItem::class);
    }
}
