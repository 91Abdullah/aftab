<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
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

    public function user_logins()
    {
        return $this->hasMany('App\UserLogin');
    }

    public function roles()
    {
        return $this->belongsToMany('App\Role');
    }

    public function endpoints()
    {
        return $this->belongsToMany('App\PsEndpoint', 'endpoint_user', 'user_id', 'ps_endpoint_id');
    }

    public function auths()
    {
        return $this->belongsToMany('App\PsAuth', 'endpoint_user', 'user_id', 'ps_endpoint_id');
    }

    public function aors()
    {
        return $this->belongsToMany('App\PsAor', 'endpoint_user', 'user_id', 'ps_endpoint_id');
    }

    public function schedule_calls()
    {
        return $this->hasMany('App\ScheduleCall', 'user_id', 'id');
    }
}
