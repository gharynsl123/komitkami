<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengambilanSampleProdukJadi extends Model
{
    use HasFactory;

    protected $table = 'pengambilan_sample_produkjadi';
    protected $guarded = [];
    public function produksi()
    {
        return $this->belongsTo(Produksi::class, 'produksi_id');
    }
}
