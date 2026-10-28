<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivityTrait;

class SpesifikasiUjiMaterial extends Model
{
    use HasFactory;
    use LogsActivityTrait;


    protected $table = 'spesifikasi_uji_material';

    protected $guarded = [];

    public function inventory() {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }

    public function manufacturer() {
        return $this->belongsTo(Manufacturers::class, 'manufacturer_id');
    }
}
