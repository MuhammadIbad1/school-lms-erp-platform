<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentHostel extends Model
{
    protected $table = 'student_hostel';

    protected $fillable = [
        'student_id',
        'room_id',
    ];

    public function student()
    {
        return $this->belongsTo(StudentProfile::class, 'student_id');
    }

    public function room()
    {
        return $this->belongsTo(HostelRoom::class, 'room_id');
    }
}
