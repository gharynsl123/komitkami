<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryInvoices extends Model
{
    use HasFactory;
    protected $table = 'delivery_invoices';
    protected $guarded = [];

    public function delivery()
    {
        return $this->belongsTo(Deliveries::class, 'delivery_id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoices::class, 'invoice_id');
    }

    public function deliveryItem()
    {
        return $this->belongsTo(DeliveryItems::class, 'delivery_item_id');
    }

    public function manualItems()
    {
        return $this->hasMany(ManualInvoiceItem::class, 'delivery_invoice_id');
    }
}
