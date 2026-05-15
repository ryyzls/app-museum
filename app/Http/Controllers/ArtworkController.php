<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\Category;
use App\Models\Museum;
use App\Models\Artwork;
use Illuminate\Http\Request;

class ArtworkController extends Controller
{
    public function index(Request $request)
    {
        // Base query - hanya artwork berkualitas
        $query = Artwork::with(['artist', 'category', 'museum'])

            ->whereNotNull('image_url')

            ->whereNotNull('description')

            ->whereRaw('TRIM(description) != ""')

            ->whereRaw('LENGTH(description) > 15')

            ->where('description', '!=', 'Imported from API')
            ->where('description', '!=', 'No description available')
            ->where('description', '!=', 'Description unavailable')
            ->where('description', '!=', 'Unknown')

            ->whereHas('artist')
            ->whereHas('category');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhereHas('artist', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by Category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by Artist
        if ($request->filled('artist')) {
            $query->where('artist_id', $request->artist);
        }

        // Sort
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'a-z':
                $query->orderBy('title', 'asc');
                break;
            case 'z-a':
                $query->orderBy('title', 'desc');
                break;
            default: // newest
                $query->latest();
        }

        $artworks = $query->paginate(12)->withQueryString();

        // Get categories & artists yang ONLY punya artwork berkualitas
        $categories = Category::whereHas('artworks', function ($q) {

            $q->whereNotNull('image_url')

                ->whereNotNull('description')

                ->whereRaw('TRIM(description) != ""')

                ->whereRaw('LENGTH(TRIM(description)) > 15');

        })
            ->orderBy('name')
            ->get();

        $artists = Artist::whereHas('artworks', function ($q) {

            $q->whereNotNull('image_url')

                ->whereNotNull('description')

                ->whereRaw('TRIM(description) != ""')

                ->whereRaw('LENGTH(TRIM(description)) > 15');

        })
            ->orderBy('name')
            ->get();

        return view('artworks.index', compact('artworks', 'categories', 'artists'));
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
