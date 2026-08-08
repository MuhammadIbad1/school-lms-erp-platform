<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudyMaterial extends Model
{
    protected $fillable = [
        'subject_id',
        'section_id',
        'teacher_id',
        'title',
        'description',
        'file_path',
        'file_type',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function teacher()
    {
        return $this->belongsTo(TeacherProfile::class, 'teacher_id');
    }
}
