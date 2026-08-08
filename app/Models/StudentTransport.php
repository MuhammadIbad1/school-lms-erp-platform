<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentTransport extends Model
{
    protected $table = 'student_transport';

    protected $fillable = [
        'student_id',
        'route_id',
        'vehicle_id',
    ];

    public function student()
    {
        return $this->belongsTo(StudentProfile::class, 'student_id');
    }

    public function route()
    {
        return $this->belongsTo(TransportRoute::class, 'route_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }
}
