<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Sparepart;
use App\Models\User;

class InventoryTransaction extends Model
{
    protected $fillable = ['sparepart_id', 'type', 'quantity', 'notes', 'user_id'];

    public function sparepart()
    {
        return $this->belongsTo(Sparepart::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
