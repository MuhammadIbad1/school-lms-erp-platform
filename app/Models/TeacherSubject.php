<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherSubject extends Model
{
    protected $table = 'teacher_subject';

    protected $fillable = [
        'teacher_id',
        'subject_id',
        'section_id',
    ];

    public function teacher()
    {
        return $this->belongsTo(TeacherProfile::class, 'teacher_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }
}
