<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResponseCode extends Model
{
    protected $fillable = ['code', 'name', 'desc'];

    public function cdrs()
    {
        return $this->belongsToMany('App\Cdr', 'cdr_response_codes', 'code','call_id', 'code', 'userfield');
    }
}
