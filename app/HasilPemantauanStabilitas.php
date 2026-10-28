<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasilPemantauanStabilitas extends Model
{
    use HasFactory;

    protected $table = 'pemantauan_stabilitas_hasil';
    protected $guarded = [];

    public function detail()
    {
        return $this->belongsTo(DetailPemantauanStabilitas::class, 'pemantauan_stabilitas_detail_id');
    }

    public function qc()
    {
        return $this->belongsTo(QualityControl::class, 'quality_control_id');
    }
}
