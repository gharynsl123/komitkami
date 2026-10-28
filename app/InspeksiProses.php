<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InspeksiProses extends Model
{
    protected $table = 'inspeksi_proses';
    protected $guarded = [];

    public function produksi()
    {
        return $this->belongsTo(Produksi::class, 'produksi_id');
    }

    public function itemInspeksiProses()
    {
        return $this->belongsTo(ItemInspeksiProses::class, 'item_inspeksi_proses_id');
    }
}
