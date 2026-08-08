<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParentProfile extends Model
{
    protected $fillable = [
        'user_id',
        'occupation',
        'identity_card_no',
        'address',
        'emergency_contact',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function students()
    {
        return $this->hasMany(StudentProfile::class, 'parent_id');
    }
}
