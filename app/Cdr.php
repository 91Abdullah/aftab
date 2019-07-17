<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cdr extends Model
{
    protected $table = 'cdr';
    protected $casts = [
        'duration' => 'string'
    ];

    public function response_codes()
    {
        return $this->belongsToMany('App\ResponseCode', 'cdr_response_codes', 'call_id', 'code', 'userfield', 'code');
    }
}
