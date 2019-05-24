<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PsAor extends Model
{
    protected $table = 'ps_aors';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function users()
    {
        return $this->belongsToMany('App\User', 'endpoint_user', 'ps_endpoint_id', 'user_id');
    }
}
