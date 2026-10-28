<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockIn extends Model
{
    use HasFactory;

    protected $table = 'stock_in';
    protected $guarded = [];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'tanggal_ed' => 'date',
    ];

    // relasi
    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }
    
    public function stokTersisa() {
        $totalKeluar = $this->stockOuts()->sum('jumlah');
        return $this->stok - $totalKeluar;
    }

    // relasi ke stockOut
    public function stockOuts() {
        return $this->hasMany(StockOut::class, 'stock_in_id');
    }

    public function stockOpnames() {
        return $this->hasMany(StockOpname::class, 'stock_in_id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function suplierMaterial() {
        return $this->belongsTo(SuplierMaterial::class, 'suplier_material_id');
    }

    public function produsen() {
        return $this->belongsTo(Manufacturers::class, 'produsen_id');
    }

    /**
     * Relasi ke batch asal (untuk koreksi stock opname).
     * Jika stock_in ini adalah koreksi dari opname, parentBatch menunjuk ke batch yang dikoreksi.
     */
    public function parentBatch() {
        return $this->belongsTo(StockIn::class, 'parent_stock_in_id');
    }

    public function childCorrections() {
        return $this->hasMany(StockIn::class, 'parent_stock_in_id');
    }

    // Pengecekan Material relationships
    public function pmPengambilanSampel() {
        return $this->hasOne(PmPengambilanSampel::class, 'stock_in_id');
    }

    public function pmInspeksiPenerimaan() {
        return $this->hasOne(PmInspeksiPenerimaan::class, 'stock_in_id');
    }

    public function pmPengujian() {
        return $this->hasOne(PmPengujian::class, 'stock_in_id');
    }

    public function pmLaporanInspeksi() {
        return $this->hasOne(PmLaporanInspeksi::class, 'stock_in_id');
    }

    // Status checker for the view
    public function statusPengecekan() {
        if ($this->pmLaporanInspeksi) {
            return 'Selesai';
        } elseif ($this->pmPengujian) {
            return 'Pengujian';
        } elseif ($this->pmInspeksiPenerimaan) {
            return 'Inspeksi Penerimaan';
        } elseif ($this->pmPengambilanSampel) {
            return 'Pengambilan Sampel';
        }
        return 'Belum Diperiksa';
    }

}
