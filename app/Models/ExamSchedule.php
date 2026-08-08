<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamSchedule extends Model
{
    protected $fillable = [
        'exam_id',
        'subject_id',
        'section_id',
        'exam_date',
        'start_time',
        'end_time',
        'room_number',
    ];

    protected $casts = [
        'exam_date' => 'date',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }
}
