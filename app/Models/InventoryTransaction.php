<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Sparepart;
use App\Models\User;

class InventoryTransaction extends Model
{
    protected $guarded = [];

    public function sparepart()
    {
        return $this->belongsTo(Sparepart::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
