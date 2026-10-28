<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\LogsActivityTrait;

class User extends Authenticatable
{
    use Notifiable;
    use LogsActivityTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'users';
    protected $guarded = [];
    protected $username = 'username';

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function roles() {
        return $this->belongsToMany(Role::class);
    }

    public function hasRole($role)
    {
        return $this->roles->contains('role_name', $role);
    }

    public function hasAnyRole($roles)
    {
        if (is_array($roles)) {
            return $this->roles->pluck('role_name')->intersect($roles)->isNotEmpty();
        }

        return $this->hasRole($roles);
    }


    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'user_id');
    }
}
