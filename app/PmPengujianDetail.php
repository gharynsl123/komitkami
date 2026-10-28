<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PmPengujianDetail extends Model
{
    use HasFactory;

    protected $table = 'pm_pengujian_detail';
    protected $guarded = [];

    public function pengujian()
    {
        return $this->belongsTo(PmPengujian::class, 'pm_pengujian_id');
    }
}
