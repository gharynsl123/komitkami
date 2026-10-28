<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Produksi extends Model
{
    protected $table = 'produksi';
    protected $guarded = [];

    protected $casts = [
        'tanggal_produksi' => 'date',
    ];
    
    function product() {
        return $this->belongsTo('App\Product', 'product_id');
    }
    
    function invoice() {
        return $this->belongsTo('App\Invoice', 'invoice_id');
    }
    
    function bahanBaku() {
        return $this->belongsTo('App\Formula', 'formula_bahan_baku_id');
    }
    function bahanKemas() {
        return $this->belongsTo('App\Formula', 'formula_bahan_kemas_id');
    }
    
    function produkJadi() {
        return $this->hasOne('App\CheckSerahTerimaBarang', 'produksi_id');
    }
    
    function barangJadi() {
        return $this->hasOne('App\CheckSerahTerimaBarang', 'produksi_id');
    }

    // Tracking Progress
    public function checkKebersihan()
    {
        return $this->hasMany(PemeriksaanKebersihan::class);
    }
    public function checkPenimbangan()
    {
        return $this->hasMany(PenimbanganBahanBaku::class);
    }
    public function checkProses()
    {
        return $this->hasMany(CheckTahapanProses::class);
    }

    public function checkQualityControl()
    {
        return $this->hasMany(QualityControlCheck::class);
    }

    public function checkRekonsiliasi()
    {
        return $this->hasMany(RekonsiliasiBahanKemas::class);
    }

    public function riwayatAlat()
    {
        return $this->hasMany(RiwayatAlat::class, 'produksi_id');
    }

    public function materialProduksi()
    {
        return $this->hasMany(MaterialProduksi::class, 'produksi_id');
    }

    public function pemantauanStabilitas()
    {
        return $this->hasOne(PemantauanStabilitas::class, 'produksi_id');
    }
}