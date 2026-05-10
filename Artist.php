<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Artist extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'bio',
        'birth_date',
        'death_date',
        'nationality',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'death_date' => 'date',
    ];

    public function artworks()
    {
        return $this->hasMany(Artwork::class);
    }
}