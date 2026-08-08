<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = [
        'vehicle_number',
        'driver_name',
        'driver_phone',
        'capacity',
    ];

    public function studentTransports()
    {
        return $this->hasMany(StudentTransport::class, 'vehicle_id');
    }
}
