<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResponseCode extends Model
{
    protected $fillable = ['code', 'name', 'desc'];
}
