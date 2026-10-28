<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SampleInProses extends Model
{
    protected $table = 'sample_in_proses';
    protected $guarded = [];

    public function produksi()
    {
        return $this->belongsTo(Produksi::class, 'produksi_id');
    }
}
