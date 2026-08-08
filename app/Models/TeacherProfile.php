<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherProfile extends Model
{
    protected $fillable = [
        'user_id',
        'employee_code',
        'qualification',
        'designation',
        'joining_date',
        'basic_salary',
        'address',
    ];

    protected $casts = [
        'joining_date' => 'date',
        'basic_salary' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function teacherSubjects()
    {
        return $this->hasMany(TeacherSubject::class, 'teacher_id');
    }

    public function timetables()
    {
        return $this->hasMany(Timetable::class, 'teacher_id');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'teacher_id');
    }

    public function studyMaterials()
    {
        return $this->hasMany(StudyMaterial::class, 'teacher_id');
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class, 'teacher_id');
    }
}
