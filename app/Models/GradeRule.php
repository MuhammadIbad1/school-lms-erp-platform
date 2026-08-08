<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradeRule extends Model
{
    protected $fillable = [
        'grade_name',
        'min_percentage',
        'max_percentage',
        'grade_point',
        'remarks',
    ];

    protected $casts = [
        'min_percentage' => 'decimal:2',
        'max_percentage' => 'decimal:2',
        'grade_point' => 'decimal:2',
    ];

    public static function getGradeForScore(float $percentage): ?self
    {
        return self::where('min_percentage', '<=', $percentage)
            ->where('max_percentage', '>=', $percentage)
            ->orderByDesc('grade_point')
            ->first();
    }
}
