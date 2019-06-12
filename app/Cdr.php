<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cdr extends Model
{
    protected $table = 'cdr';
    protected $casts = [
        'duration' => 'string'
    ];
}
