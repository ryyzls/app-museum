<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'exhibition_id',
        'type',
        'price',
        'stock',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function exhibition()
    {
        return $this->belongsTo(Exhibition::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}