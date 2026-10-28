<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PmLaporanInspeksiDetail extends Model
{
    use HasFactory;

    protected $table = 'pm_laporan_inspeksi_detail';
    protected $guarded = [];
    
    protected $casts = [
        'hasil_json' => 'array',
    ];

    public function laporan()
    {
        return $this->belongsTo(PmLaporanInspeksi::class, 'pm_laporan_inspeksi_id');
    }
}
