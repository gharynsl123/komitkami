<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogWebsite extends Model
{
    use HasFactory;
    protected $table = 'log_website';
    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime:d/m/Y H:i',
        'updated_at' => 'datetime:d/m/Y H:i',
    ];
    
    public function users() {
        return $this->belongsTo('App\User', 'users_id');
    }
}
