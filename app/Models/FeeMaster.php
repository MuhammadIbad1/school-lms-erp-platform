<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeMaster extends Model
{
    protected $fillable = [
        'class_id',
        'fee_group_id',
        'amount',
        'due_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
    ];

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function feeGroup()
    {
        return $this->belongsTo(FeeGroup::class, 'fee_group_id');
    }
}
