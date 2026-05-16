<?php

namespace App\Http\Controllers;

use App\Models\Exhibition;

class ExhibitionController extends Controller
{
    public function index()
    {
        $exhibitions = Exhibition::with([
            'museum',
            'artworks'
        ])
            ->latest()
            ->paginate(12);

        return view('exhibitions.index', compact('exhibitions'));
    }

    public function show($id)
    {
        $exhibition = Exhibition::with([
            'museum',
            'artworks.artist',
            'artworks.category',
            'tickets'
        ])->findOrFail($id);

        return view('exhibitions.show', compact('exhibition'));
    }
}