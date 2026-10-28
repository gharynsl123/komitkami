<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MaterialProduksi extends Model
{
    protected $table = 'permintaan_material';
    protected $guarded = [];

    function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    function bahanbaku()
    {
        return $this->belongsTo('App\FormulaBahanBaku', 'formula_bahan_baku_id');
    }

    function bahankemas()
    {
        return $this->belongsTo('App\FormulaBahanKemas', 'formula_bahan_kemas_id');
    }
    function produksi()
    {
        return $this->belongsTo(Produksi::class, 'produksi_id');
    }

    function inventory()
    {
        return $this->belongsTo('App\Inventory', 'inventory_id');
    }

    function stockOuts()
    {
        return $this->hasMany(StockOut::class, 'permintaan_id');
    }
}
