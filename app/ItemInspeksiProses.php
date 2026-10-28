<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemInspeksiProses extends Model
{
    use HasFactory;

    protected $table = 'item_inspeksi_proses';
    protected $guarded = [];

    public function produksi()
    {
        return $this->belongsTo(Produksi::class, 'produksi_id');
    }
    public function inspeksiProses()
    {
        return $this->belongsTo(InspeksiProses::class, 'inspeksi_proses_id');
    }
    public function acuanLaporanIpc()
    {
        return $this->belongsTo(AcuanLaporanIpc::class, 'acuan_laporan_ipc_id');
    }
}
