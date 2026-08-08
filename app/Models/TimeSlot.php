<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeSlot extends Model
{
    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'is_break',
    ];

    protected $casts = [
        'is_break' => 'boolean',
    ];

    public function timetables()
    {
        return $this->hasMany(Timetable::class);
    }
}
