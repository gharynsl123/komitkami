<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PmInspeksiPenerimaan extends Model
{
    use HasFactory;

    protected $table = 'pm_inspeksi_penerimaan';
    protected $guarded = [];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function stockIn()
    {
        return $this->belongsTo(StockIn::class, 'stock_in_id');
    }

    public function manufacturer()
    {
        return $this->belongsTo(Manufacturers::class, 'manufacturer_id');
    }

    public function manualReport()
    {
        return $this->belongsTo(PmManualReport::class, 'pm_manual_report_id');
    }
}