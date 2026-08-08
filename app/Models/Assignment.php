<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    protected $fillable = [
        'subject_id',
        'section_id',
        'teacher_id',
        'title',
        'description',
        'attachment_path',
        'due_date',
        'max_marks',
    ];

    protected $casts = [
        'due_date' => 'datetime',
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

    public function submissions()
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    public function getIsOverdueAttribute(): bool
    {
        return now()->isAfter($this->due_date);
    }
}
