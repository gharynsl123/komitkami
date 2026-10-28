<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOutProduct extends Model
{
    use HasFactory;

    protected $table = 'stock_out_product';
    protected $guarded = [];

    protected $casts = [
        'quantity' => 'decimal:3', // konsisten dengan decimal(10,3)
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function deliveryItem()
    {
        return $this->belongsTo(DeliveryItems::class, 'delivery_item_id');
    }

    public function stockInProduct()
    {
        return $this->belongsTo(StockInProduct::class, 'stock_in_product_id');
    }

    // ── Scopes untuk filter berdasarkan kategori ──────────────────────────────

    /**
     * Hanya stock out produk dari penyesuaian stock opname.
     */
    public function scopeOpname($query)
    {
        return $query->where('kategori', 'stock_opname');
    }

    /**
     * Hanya stock out produk dari pengiriman/delivery (bukan opname).
     */
    public function scopeDelivery($query)
    {
        return $query->where(function ($q) {
            $q->where('kategori', '!=', 'stock_opname')
              ->orWhereNull('kategori');
        });
    }

    /**
     * Filter berdasarkan kategori tertentu.
     * Contoh: StockOutProduct::byKategori('stock_opname')->get()
     */
    public function scopeByKategori($query, string $kategori)
    {
        return $query->where('kategori', $kategori);
    }
}
