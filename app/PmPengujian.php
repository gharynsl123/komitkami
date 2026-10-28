<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PmPengujian extends Model
{
    use HasFactory;

    protected $table = 'pm_pengujian';
    protected $guarded = [];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function stockIn()
    {
        return $this->belongsTo(StockIn::class, 'stock_in_id');
    }

    public function details()
    {
        return $this->hasMany(PmPengujianDetail::class, 'pm_pengujian_id');
    }

    public function manualReport()
    {
        return $this->belongsTo(PmManualReport::class, 'pm_manual_report_id');
    }
}