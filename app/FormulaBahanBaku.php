<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivityTrait;

class FormulaBahanBaku extends Model
{
    use LogsActivityTrait;
    
    protected $table = 'formula_bahan_baku';
    protected $guarded = [];

    function formula() {
        return $this->belongsTo('App\Formula', 'formula_id');
    }

    public function inventory() {
        return $this->belongsTo('App\Inventory', 'inventory_id');
    }
}
