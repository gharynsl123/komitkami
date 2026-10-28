<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatAlat extends Model
{
    use HasFactory;

    protected $table = 'riwayat_alat';
    protected $guarded = [];

    public function produksi()
    {
        return $this->belongsTo(Produksi::class, 'produksi_id');
    }

    public function ruangProduksi()
    {
        return $this->belongsTo(RuangProduksi::class, 'ruang_produksi_id');
    }

}
