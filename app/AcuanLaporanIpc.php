<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivityTrait;

class AcuanLaporanIpc extends Model
{
    use HasFactory;

    use LogsActivityTrait;
    protected $table = 'acuan_laporan_ipc';
    protected $guarded = [];

    public function formula()
    {
        return $this->belongsTo(Formula::class, 'formula_id');
    }
}
