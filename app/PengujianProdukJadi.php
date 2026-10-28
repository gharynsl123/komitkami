<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengujianProdukJadi extends Model
{
    use HasFactory;

    protected $table = 'pengujian_produk_jadi';
    protected $guarded = [];
    public function produksi()
    {
        return $this->belongsTo(Produksi::class, 'produksi_id');
    }
}
