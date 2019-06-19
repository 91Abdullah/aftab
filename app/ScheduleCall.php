<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ScheduleCall extends Model
{
    protected $fillable = ['schedule_time', 'number'];
    protected $casts = [
        'status' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo('App\User','user_id', 'id');
    }

    public function getScheduleTimeAttribute($value)
    {
        return Carbon::parse($value);
    }

    public function scopeExpired($query, User $user)
    {
        return $query->where([
            ['schedule_time', '<', Carbon::now()],
            ['status', false]
        ]);
    }
}
