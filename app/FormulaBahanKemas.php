<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivityTrait;

class FormulaBahanKemas extends Model
{
    use LogsActivityTrait;
    protected $table = 'formula_bahan_kemas';
    protected $guarded = [];

    function formula() {
        return $this->belongsTo('App\Formula', 'formula_id');
    }

    public function inventory() {
        return $this->belongsTo('App\Inventory', 'inventory_id');
    }
}
