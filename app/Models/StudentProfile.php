<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentProfile extends Model
{
    protected $fillable = [
        'user_id',
        'parent_id',
        'class_id',
        'section_id',
        'roll_number',
        'admission_number',
        'admission_date',
        'date_of_birth',
        'gender',
        'blood_group',
        'address',
    ];

    protected $casts = [
        'admission_date' => 'date',
        'date_of_birth' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(ParentProfile::class, 'parent_id');
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'student_id');
    }

    public function marks()
    {
        return $this->hasMany(Mark::class, 'student_id');
    }

    public function assignmentSubmissions()
    {
        return $this->hasMany(AssignmentSubmission::class, 'student_id');
    }

    public function feeInvoices()
    {
        return $this->hasMany(FeeInvoice::class, 'student_id');
    }

    public function transport()
    {
        return $this->hasOne(StudentTransport::class, 'student_id');
    }

    public function hostel()
    {
        return $this->hasOne(StudentHostel::class, 'student_id');
    }

    public function getAttendancePercentageAttribute(): float
    {
        $total = $this->attendances()->count();
        if ($total === 0) return 100.0;
        $present = $this->attendances()->whereIn('status', ['present', 'late'])->count();
        return round(($present / $total) * 100, 1);
    }
}
