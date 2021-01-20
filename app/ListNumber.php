<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ListNumber extends Model
{
    protected $fillable = ['number', 'upload_list_id', 'name', 'city'];
    public $timestamps = false;

    public function parent()
    {
        return $this->belongsTo('App\UploadList', 'upload_list_id');
    }
}
