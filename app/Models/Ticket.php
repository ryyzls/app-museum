<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'exhibition_id',
        'ticket_type',
        'price',
        'quota',
        'available_quota',
        'visit_date',
        'status',
    ];

    protected $casts = [
        'visit_date' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function exhibition()
    {
        return $this->belongsTo(Exhibition::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}