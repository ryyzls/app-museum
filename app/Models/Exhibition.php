<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Carbon;

class Exhibition extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'banner_image',
        'start_date',
        'end_date',
        // 'status', <-- HAPUS DARI FILLABLE agar tidak bisa diinput manual
        'museum_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // Otomatis menyertakan 'computed_status' saat model diubah ke Array / JSON (API)
    protected $appends = ['computed_status'];

    /*

    |--------------------------------------------------------------------------
    | SINGLE SOURCE OF TRUTH STATUS
    |--------------------------------------------------------------------------
    */

    protected function computedStatus(): Attribute
    {
        return Attribute::get(function () {
            $today = Carbon::today();

            $start = $this->start_date;
            $end = $this->end_date;

            if (!$start || !$end) {
                return 'Unknown';
            }

            if ($today->lt($start)) {
                return 'Upcoming';
            }

            if ($today->betweenIncluded($start, $end)) {
                return 'Current';
            }

           
            return 'Past';
        });
    }

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
