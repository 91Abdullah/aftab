<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Process extends Model
{
    protected $fillable = ['desc', 'starttime', 'endtime', 'execution_time', 'call_time', 'generated_nums', 'db_nums'];
}
