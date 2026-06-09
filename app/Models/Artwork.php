<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Review;

class Artwork extends Model
{
    use HasFactory;

    protected $fillable = [
        'ark_id',
        'title',
        'description',
        'image_url',
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

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function exhibitions()
    {
        return $this->belongsToMany(
            Exhibition::class,
            'exhibition_artworks'
        );
    }
}