<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    protected $fillable = [
        'title',
        'content',
        'target_role',
        'created_by',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
