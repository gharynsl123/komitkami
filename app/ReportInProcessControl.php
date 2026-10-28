<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportInProcessControl extends Model
{
    use HasFactory;

    protected $table = 'report_in_process_control';
    protected $guarded = [];
    public function produksi()
    {
        return $this->belongsTo(Produksi::class, 'produksi_id');
    }

    public function itemReportInProcess()
    {
        return $this->belongsTo(ItemReportInPC::class, 'item_report_in_process_id');
    }
}
