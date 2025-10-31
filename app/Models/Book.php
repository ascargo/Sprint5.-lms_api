<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'title',
        'author',
        'isbn',
        'year',
        'genre',
        'collection',
        'location',
        'cover_path',
        //'status_id',
    ];

    //public function status()
    //{
    //return $this->belongsTo(BookStatus::class);
    //}

    public function books()
    {
        return $this->hasMany(Book::class);
    }
}
