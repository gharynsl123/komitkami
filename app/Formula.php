<?php

namespace App;

use App\Traits\LogsActivityTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formula extends Model
{
    use HasFactory;
    use LogsActivityTrait;

    protected $table = 'formula';
    protected $guarded = [];

    public function bahanBaku()
    {
        return $this->hasMany(FormulaBahanBaku::class);
    }

    public function bahanKemas()
    {
        return $this->hasMany(FormulaBahanKemas::class);
    }

    public function tahapanProses()
    {
        return $this->hasMany(TahapanProses::class);
    }

    public function qualityControl()
    {
        return $this->hasMany(QualityControl::class);
    }

    public function acuanLaporanIpc()
    {
        return $this->hasMany(AcuanLaporanIpc::class);
    }
}