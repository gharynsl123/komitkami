<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockInProduct extends Model
{
    use HasFactory;

    protected $table = 'stock_in_product';
    protected $guarded = [];

    public function produksi()
    {
        return $this->belongsTo(Produksi::class, 'produksi_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function stockOuts()
    {
        return $this->hasMany(StockOutProduct::class, 'stock_in_product_id');
    }

    /**
     * Relasi ke batch asal (untuk koreksi stock opname produk jadi).
     */
    public function parentBatch()
    {
        return $this->belongsTo(StockInProduct::class, 'parent_stock_in_product_id');
    }

    public function childCorrections()
    {
        return $this->hasMany(StockInProduct::class, 'parent_stock_in_product_id');
    }
}
