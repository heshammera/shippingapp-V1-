<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryMovement extends Model
{
    protected $fillable = ['product_id','shipment_id','qty_change','reason','meta'];
    protected $casts = ['meta'=>'array'];
}
