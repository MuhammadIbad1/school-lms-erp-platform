<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HostelRoom extends Model
{
    protected $fillable = [
        'hostel_id',
        'room_number',
        'capacity',
        'cost_per_bed',
    ];

    protected $casts = [
        'cost_per_bed' => 'decimal:2',
    ];

    public function hostel()
    {
        return $this->belongsTo(Hostel::class);
    }

    public function students()
    {
        return $this->hasMany(StudentHostel::class, 'room_id');
    }
}
