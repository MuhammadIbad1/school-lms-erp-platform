<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolClass extends Model
{
    protected $table = 'classes';

    protected $fillable = [
        'name',
        'numeric_code',
    ];

    public function sections()
    {
        return $this->hasMany(Section::class, 'class_id');
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class, 'class_id');
    }

    public function studentProfiles()
    {
        return $this->hasMany(StudentProfile::class, 'class_id');
    }

    public function feeMasters()
    {
        return $this->hasMany(FeeMaster::class, 'class_id');
    }
}
