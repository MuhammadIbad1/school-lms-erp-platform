<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'title',
        'isbn',
        'author',
        'publisher',
        'quantity',
        'rack_number',
    ];

    public function issues()
    {
        return $this->hasMany(BookIssue::class);
    }
}
