<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    protected $fillable = [
        'teacher_id',
        'month_year',
        'basic_salary',
        'allowances',
        'deductions',
        'net_salary',
        'status',
        'paid_at',
        'payment_method',
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'allowances' => 'decimal:2',
        'deductions' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'paid_at' => 'date',
    ];

    public function teacher()
    {
        return $this->belongsTo(TeacherProfile::class, 'teacher_id');
    }
}
