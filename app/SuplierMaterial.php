<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\LogsActivityTrait;

class SuplierMaterial extends Model
{
    use HasFactory;
    use LogsActivityTrait;

    protected $table = "suplier_material";
    protected $guarded = [];

    public function inventory() {
        $this->BelongsTo(Inventory::class, 'inventory_id');
    }
}
