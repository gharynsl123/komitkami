<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasilMikrobiologi extends Model
{
    use HasFactory;

    protected $table = 'hasil_uji_mikrobiologi';
    protected $guarded = [];

    protected $casts = [
        'tanggal'    => 'date',
        'is_manual'  => 'boolean',
        'awal_cfu_ml' => 'float',
        'awal_log'    => 'float',
        'clean_cfu_ml' => 'float',
        'clean_log'    => 'float',
        'dirty_cfu_ml' => 'float',
        'dirty_log'    => 'float',
        'persen_clean' => 'float',
        'persen_dirty' => 'float',
    ];

    /**
     * Relasi ke tabel produksi (nullable untuk data manual).
     */
    public function produksi()
    {
        return $this->belongsTo(Produksi::class, 'produksi_id');
    }

    /**
     * Scope: ambil hanya data yang terhubung ke produksi
     */
    public function scopeFromProduksi($query)
    {
        return $query->whereNotNull('produksi_id')->where('is_manual', false);
    }

    /**
     * Scope: ambil hanya data manual (tidak terhubung ke produksi)
     */
    public function scopeManual($query)
    {
        return $query->where('is_manual', true);
    }
}

