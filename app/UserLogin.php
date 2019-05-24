<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserLogin extends Model
{
    protected $fillable = ['session_id', 'user_id', 'login_time', 'logout_time'];
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
