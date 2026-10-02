<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Sparepart extends Model
{
    use SoftDeletes;
    protected $fillable = ['part_number', 'name', 'description', 'brand', 'stock', 'reorder_level', 'price'];
}
