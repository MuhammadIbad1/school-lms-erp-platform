<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $fillable = [
        'academic_year_id',
        'name',
        'start_date',
        'end_date',
        'is_published',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_published' => 'boolean',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function schedules()
    {
        return $this->hasMany(ExamSchedule::class);
    }

    public function marks()
    {
        return $this->hasMany(Mark::class);
    }
}
