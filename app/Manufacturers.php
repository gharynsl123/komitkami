<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivityTrait;

class Manufacturers extends Model
{
    use HasFactory;
    use LogsActivityTrait;

    protected $table = "manufacturers";
    protected $guarded = [];

    public function specifications() {
        return $this->hasMany(SpesifikasiUjiMaterial::class, 'manufacturer_id');
    }

    public function inventory() {
        return $this->belongsTo(Inventory::class,'inventory_id');
    }
}
