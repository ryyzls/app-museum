<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Artwork extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'image',
        'artist_id',
        'category_id',
        'museum_id',
    ];

    public function artist()
    {
        return $this->belongsTo(Artist::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function museum()
    {
        return $this->belongsTo(Museum::class);
    }

    public function exhibitions()
    {
        return $this->belongsToMany(
            Exhibition::class,
            'exhibition_artworks'
        );
    }
}