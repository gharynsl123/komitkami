<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOpname extends Model
{
    use HasFactory;

    protected $table = 'stock_opnames';
    protected $guarded = [];

    protected $casts = [
        'tanggal_opname' => 'date',
    ];

    public function inventory()
    {
        return $this->belongsTo('App\Inventory', 'inventory_id');
    }

    public function stockIn()
    {
        return $this->belongsTo('App\StockIn', 'stock_in_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function product()
    {
        return $this->belongsTo('App\Product', 'product_id');
    }

    public function stockInProduct()
    {
        return $this->belongsTo('App\StockInProduct', 'stock_in_product_id');
    }

    /**
     * Cek apakah opname ini adalah koreksi positif (ada stock_in turunan dengan nomor_sj OPNAME-{id})
     */
    public function getIsKoreksiPositifAttribute(): bool
    {
        return \App\StockIn::where('nomor_sj', 'OPNAME-' . $this->id)->exists()
            || \App\StockInProduct::where('note', 'like', '%OPNAME - #' . $this->id . '%')->exists();
    }
}
