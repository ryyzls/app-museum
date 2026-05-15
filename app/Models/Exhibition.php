<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exhibition extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'banner_image',
        'start_date',
        'end_date',
        'status',
        'museum_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

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