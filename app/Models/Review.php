<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Artwork;

class Review extends Model
{
    protected $fillable = ['user_id', 'artwork_id', 'rating', 'comment'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function artwork()
    {
        return $this->belongsTo(Artwork::class);
    }
}
