<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivityTrait;

class TahapanProses extends Model
{
    use LogsActivityTrait;
    protected $table = 'tahapan_proses';
    protected $guarded = [];

    public function formula() {
        return $this->belongsTo('App\Formula', 'formula_id');
    }
}
