<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivityTrait;

class QualityControl extends Model
{
    protected $table = 'quality_control';
    protected $guarded = [];
    use LogsActivityTrait;

    public function formula() {
        return $this->belongsTo('App\Formula', 'formula_id');
    }
}
