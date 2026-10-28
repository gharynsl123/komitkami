<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemPpj extends Model
{
    use HasFactory;

    protected $table = 'item_ppj';
    protected $guarded = [];

    public function produksi()
    {
        return $this->belongsTo(Produksi::class, 'produksi_id');
    }
    public function pengujianPJ()
    {
        return $this->belongsTo(PengujianProdukJadi::class, 'pengujian_produk_jadi_id');
    }

    public function acuanLaporanIpc()
    {
        return $this->belongsTo(AcuanLaporanIpc::class, 'acuan_laporan_ipc_id');
    }
}
