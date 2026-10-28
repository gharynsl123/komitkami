<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PmLaporanInspeksi extends Model
{
    use HasFactory;

    protected $table = 'pm_laporan_inspeksi';
    protected $guarded = [];

    protected $casts = [
        'tanggal_efektif' => 'date',
        'tanggal_inspeksi' => 'date',
    ];

    public function stockIn()
    {
        return $this->belongsTo(StockIn::class, 'stock_in_id');
    }

    public function details()
    {
        return $this->hasMany(PmLaporanInspeksiDetail::class, 'pm_laporan_inspeksi_id');
    }

    public function manualReport()
    {
        return $this->belongsTo(PmManualReport::class, 'pm_manual_report_id');
    }
}