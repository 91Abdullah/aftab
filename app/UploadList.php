<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UploadList extends Model
{
    protected $fillable = ['name', 'active'];

    public function numbers()
    {
        return $this->hasMany('App\ListNumber', 'upload_list_id', 'id');
    }

    public function setActiveAttribute($value)
    {
        if($value == "on") {
            $this->attributes['active'] = 1;
        }
    }
}
