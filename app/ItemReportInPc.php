<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemReportInPc extends Model
{
    use HasFactory;

    protected $table = 'item_report_in_pc';
    protected $guarded = [];

    protected $casts = [
        'hasil' => 'array',
    ];

    public function produksi()
    {
        return $this->belongsTo(Produksi::class, 'produksi_id');
    }
    public function reportIPC()
    {
        return $this->belongsTo(ReportInProcessControl::class, 'report_in_process_control_id');
    }
    public function acuanLaporanIpc()
    {
        return $this->belongsTo(AcuanLaporanIpc::class, 'acuan_laporan_ipc_id');
    }
}
