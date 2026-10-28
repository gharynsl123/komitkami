<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deliveries extends Model
{
    use HasFactory;
    protected $table = 'deliveries';
    protected $guarded = [];

    public function items()
    {
        return $this->hasMany(DeliveryItems::class, 'delivery_id');
    }


    public function invoices()
    {
        return $this->hasMany(DeliveryInvoices::class, 'delivery_id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
}
