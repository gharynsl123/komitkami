<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemantauanStabilitas extends Model
{
    use HasFactory;

    protected $table = 'pemantauan_stabilitas';
    protected $guarded = [];

    public function produksi()
    {
        return $this->belongsTo(Produksi::class, 'produksi_id');
    }

    public function pemeriksaan_qc()
    {
        return $this->belongsTo(QualityControlCheck::class, 'pemeriksaan_qc_id');
    }

    public function detail()
    {
        return $this->hasMany(DetailPemantauanStabilitas::class, 'pemantauan_stabilitas_id');
    }
}
