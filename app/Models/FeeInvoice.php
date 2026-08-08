<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeInvoice extends Model
{
    protected $fillable = [
        'student_id',
        'invoice_number',
        'title',
        'total_amount',
        'paid_amount',
        'due_date',
        'status',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(StudentProfile::class, 'student_id');
    }

    public function payments()
    {
        return $this->hasMany(FeePayment::class);
    }

    public function getDueBalanceAttribute(): float
    {
        return (float) ($this->total_amount - $this->paid_amount);
    }
}
