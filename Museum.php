<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Museum extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'description',
    ];

    public function artworks()
    {
        return $this->hasMany(Artwork::class);
    }

    public function exhibitions()
    {
        return $this->hasMany(Exhibition::class);
    }
}