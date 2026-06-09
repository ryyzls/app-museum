<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artist;

class AdminArtistController extends Controller
{
    public function index()
    {
        $artists = Artist::withCount('artworks')

            ->orderByRaw("
            CASE
            WHEN bio IS NOT NULL
            AND bio != ''
            THEN 0
            ELSE 1
            END")

            ->orderByDesc('artworks_count')

            ->paginate(10);

        return view('admin.artists.index', compact('artists'));
    }
}