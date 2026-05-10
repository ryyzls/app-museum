<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use Illuminate\Http\Request;

class ArtworkController extends Controller
{
    public function index()
    {
        $artworks = Artwork::with(['artist', 'category', 'museum'])->get();

        return response()->json($artworks);
    }

    public function show($id)
    {
        $artwork = Artwork::with(['artist', 'category', 'museum'])
            ->findOrFail($id);

        return response()->json($artwork);
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

        $artwork = Artwork::create($validated);

        return response()->json([
            'message' => 'Artwork created successfully',
            'data' => $artwork
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $artwork = Artwork::findOrFail($id);
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes',
            'image' => 'sometimes|string',
            'artist_id' => 'sometimes|exists:artists,id',
            'category_id' => 'sometimes|exists:categories,id',
            'museum_id' => 'sometimes|exists:museums,id',
        ]);

        $artwork->update($validated);

        return response()->json([
            'message' => 'Artwork updated successfully',
            'data' => $artwork
        ]);
    }

    public function destroy($id)
    {
        $artwork = Artwork::findOrFail($id);
        $artwork->delete();

        return response()->json([
            'message' => 'Artwork deleted successfully'
        ]);
    }
}
