<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = "order";
    protected $guarded = [];

    function product() {
        return $this->belongsTo('App\Product', 'product_id');
    }
    
    public function deliveryItems()
    {
        return $this->hasMany(DeliveryItems::class, 'order_id');
    }

    function user() {
        return $this->belongsTo('App\User', 'id_user');
    }

    function invoice() {
        return $this->belongsTo('App\Invoice', 'invoice_id');
    }
}
