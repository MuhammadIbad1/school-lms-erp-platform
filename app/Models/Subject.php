<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = [
        'class_id',
        'name',
        'code',
        'pass_marks',
        'max_marks',
        'type',
    ];

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function teacherSubjects()
    {
        return $this->hasMany(TeacherSubject::class, 'subject_id');
    }

    public function marks()
    {
        return $this->hasMany(Mark::class, 'subject_id');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'subject_id');
    }

    public function studyMaterials()
    {
        return $this->hasMany(StudyMaterial::class, 'subject_id');
    }
}
