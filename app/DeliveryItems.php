<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryItems extends Model
{
    use HasFactory;
    protected $table = 'delivery_items';
    protected $guarded = [];

    public function delivery()
    {
        return $this->belongsTo(Deliveries::class, 'delivery_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function stockOuts()
    {
        return $this->hasMany(StockOutProduct::class, 'delivery_item_id');
    }
}
