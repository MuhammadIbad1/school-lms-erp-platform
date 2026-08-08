<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransportRoute extends Model
{
    protected $fillable = [
        'name',
        'fare',
        'description',
    ];

    protected $casts = [
        'fare' => 'decimal:2',
    ];

    public function studentTransports()
    {
        return $this->hasMany(StudentTransport::class, 'route_id');
    }
}
