<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'balance', 'points',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Has One to role.
     *
     */
    public function role()
    {
        return $this->hasOne(\App\Models\Role::class, 'user_id', 'id');
    }

    /**
     * Has Many to points.
     *
     */
    public function points()
    {
        return $this->hasMany(\App\Models\UserPoint::class, 'user_id', 'id');
    }

    /**
     * Has Many to user balance.
     *
     */
    public function balance()
    {
        return $this->hasMany(\App\Models\UserBalance::class, 'user_id', 'id');
    }

    /**
     * Has Many to user balance.
     *
     */
    public function transactions()
    {
        return $this->hasMany(\App\Models\Transaction::class, 'user_id', 'id');
    }
}
