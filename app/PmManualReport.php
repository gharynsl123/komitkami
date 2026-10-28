<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PmManualReport extends Model
{
    protected $table = 'pm_manual_reports';
    protected $fillable = [
        'inventory_id',
        'suplier_material_id',
        'nomor_batch',
        'tanggal_laporan'
    ];

    protected $casts = [
        'tanggal_laporan' => 'date',
    ];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    public function suplierMaterial()
    {
        return $this->belongsTo(SuplierMaterial::class);
    }

    // Detail relationships
    public function pengambilanSampel()
    {
        return $this->hasOne(PmPengambilanSampel::class, 'pm_manual_report_id');
    }

    public function inspeksiPenerimaan()
    {
        return $this->hasOne(PmInspeksiPenerimaan::class, 'pm_manual_report_id');
    }

    public function pengujian()
    {
        return $this->hasOne(PmPengujian::class, 'pm_manual_report_id');
    }

    public function laporanInspeksi()
    {
        return $this->hasOne(PmLaporanInspeksi::class, 'pm_manual_report_id');
    }
}
