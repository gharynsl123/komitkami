<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPemantauanStabilitas extends Model
{
    use HasFactory;

    protected $table = 'pemantauan_stabilitas_detail';
    protected $guarded = [];

    public function pemantauan()
    {
        return $this->belongsTo(PemantauanStabilitas::class, 'pemantauan_stabilitas_id');
    }

    public function hasil()
    {
        return $this->hasMany(HasilPemantauanStabilitas::class, 'pemantauan_stabilitas_detail_id');
    }
}
