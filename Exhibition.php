<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exhibition extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'start_date',
        'end_date',
        'museum_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function museum()
    {
        return $this->belongsTo(Museum::class);
    }

    public function artworks()
    {
        return $this->belongsToMany(
            Artwork::class,
            'exhibition_artworks'
        );
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}