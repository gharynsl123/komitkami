<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManualInvoiceItem extends Model
{
    use HasFactory;

    protected $table = 'manual_invoice_items';
    protected $guarded = [];

    public function deliveryInvoice()
    {
        return $this->belongsTo(DeliveryInvoices::class, 'delivery_invoice_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
