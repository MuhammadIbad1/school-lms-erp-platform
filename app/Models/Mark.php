<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mark extends Model
{
    protected $fillable = [
        'exam_id',
        'subject_id',
        'student_id',
        'marks_obtained',
        'remarks',
        'entered_by',
    ];

    protected $casts = [
        'marks_obtained' => 'decimal:2',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function student()
    {
        return $this->belongsTo(StudentProfile::class, 'student_id');
    }

    public function marker()
    {
        return $this->belongsTo(User::class, 'entered_by');
    }

    public function getPercentageAttribute(): float
    {
        $max = $this->subject?->max_marks ?? 100;
        return $max > 0 ? round(($this->marks_obtained / $max) * 100, 2) : 0.0;
    }

    public function getGradeAttribute(): ?GradeRule
    {
        $pct = $this->percentage;
        return GradeRule::where('min_percentage', '<=', $pct)
            ->where('max_percentage', '>=', $pct)
            ->first();
    }
}
