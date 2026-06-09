<?php

namespace App\Models;

use App\Events\OrderPlaced;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['status'];

    protected $dispatchesEvents = [
        'created' => OrderPlaced::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
