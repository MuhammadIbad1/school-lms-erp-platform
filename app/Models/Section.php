<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $fillable = [
        'class_id',
        'name',
        'capacity',
        'class_teacher_id',
    ];

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function classTeacher()
    {
        return $this->belongsTo(TeacherProfile::class, 'class_teacher_id');
    }

    public function students()
    {
        return $this->hasMany(StudentProfile::class, 'section_id');
    }

    public function teacherSubjects()
    {
        return $this->hasMany(TeacherSubject::class, 'section_id');
    }

    public function timetables()
    {
        return $this->hasMany(Timetable::class, 'section_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'section_id');
    }

    public function getFullNameAttribute(): string
    {
        return ($this->schoolClass?->name ?? 'Class') . ' - ' . $this->name;
    }
}
