<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RekonsiliasiBahanKemas extends Model
{
    protected $table = 'rekonsiliasi_bahan_kemas';
    protected $guarded = [];

    public function produksi() {
        return $this->belongsTo('App\Produksi', 'produksi_id');
    }

    public function bahanKemas() {
        return $this->belongsTo('App\FormulaBahanKemas', 'formula_bahan_kemas_id');
    }
}
