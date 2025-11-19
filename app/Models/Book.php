<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'isbn',
        'year',
        'genre',
        'collection',
        'location',
        'cover_path',
        'status',
    ];

    public function scopeStatus($query, $status)
    {
        if (!empty($status)) {
            return $query->where('status', $status);
        }
        return $query;
    }

    public function scopeAuthor($query, $author)
    {
        if (!empty($author)) {
            return $query->where('author', 'LIKE', "%{$author}%");
        }
        return $query;
    }

    public function scopeTitle($query, $title)
    {
        if (!empty($title)) {
            return $query->where('title', 'LIKE', "%{$title}%");
        }
        return $query;
    }

    public function scopeGenre($query, $genre)
    {
        if (!empty($genre)) {
            return $query->where('genre', 'LIKE', "%{$genre}%");
        }
        return $query;
    }

}
