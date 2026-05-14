<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\Category;
use App\Models\Museum;
use App\Models\Artwork;
use Illuminate\Http\Request;

class ArtworkController extends Controller
{
    public function index()
    {
        $artworks = Artwork::with(['artist', 'category', 'museum'])->get();

        return view('artworks.index', compact('artworks'));
    }

    public function show($id)
    {
        $artwork = Artwork::with(['artist', 'category', 'museum'])
            ->findOrFail($id);

        return view('artworks.show', compact('artwork'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required',
            'image' => 'required|string',
            'artist_id' => 'required|exists:artists,id',
            'category_id' => 'required|exists:categories,id',
            'museum_id' => 'required|exists:museums,id',
        ]);

        Artwork::create($validated);

        return redirect('/artworks')
            ->with('success', 'Artwork created successfully');
    }

    public function edit($id)
    {
        $artwork = Artwork::findOrFail($id);

        return view('artworks.edit', [
            'artwork' => $artwork,
            'artists' => Artist::all(),
            'categories' => Category::all(),
            'museums' => Museum::all(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $artwork = Artwork::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required',
            'image' => 'required|string',
            'artist_id' => 'required|exists:artists,id',
            'category_id' => 'required|exists:categories,id',
            'museum_id' => 'required|exists:museums,id',
        ]);

        $artwork->update($validated);

        return redirect('/artworks')
            ->with('success', 'Artwork updated successfully');
    }


    public function create()
    {
        return view('artworks.create', [
            'artists' => Artist::all(),
            'categories' => Category::all(),
            'museums' => Museum::all(),
        ]);
    }


    public function destroy($id)
    {
        $artwork = Artwork::findOrFail($id);

        $artwork->delete();

        return redirect('/artworks')
            ->with('success', 'Artwork deleted successfully');
    }
}
