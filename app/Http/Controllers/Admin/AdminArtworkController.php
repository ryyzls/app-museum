<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Artwork;

use App\Models\Artist;
use App\Models\Category;
use App\Models\Museum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminArtworkController extends Controller
{
    public function index()
    {
        $artworks = Artwork::with([

            'artist',
            'category',
            'museum'

        ])
            ->latest()
            ->paginate(12);

        return view('admin.artworks.index', compact('artworks'));
    }

    public function create()
    {
        $artists = Artist::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $museums = Museum::orderBy('name')->get();
        return view('admin.artworks.create', compact('artists', 'categories', 'museums'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate(['title' => 'required|max:255', 'artist_id' => 'required', 'category_id' => 'required', 'museum_id' => 'required', 'description' => 'nullable', 'year' => 'nullable|max:50', 'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',]);
        if ($request->hasFile('image')) {
            $validated['image_url'] = $request->file('image')->store('artworks', 'public');
        }
        Artwork::create($validated);
        return redirect()->route('admin.artworks.index')->with('success', 'Artwork created successfully.');
    }

    public function edit(Artwork $artwork)
    {
        $artists = Artist::orderBy('name')->get();

        $categories = Category::orderBy('name')->get();

        $museums = Museum::orderBy('name')->get();

        return view('admin.artworks.edit', compact(

            'artwork',
            'artists',
            'categories',
            'museums'

        ));
    }

    public function update(Request $request, Artwork $artwork)
    {
        $validated = $request->validate([

            'title' => 'required|max:255',

            'artist_id' => 'required',

            'category_id' => 'required',

            'museum_id' => 'required',

            'description' => 'nullable',

            'year' => 'nullable|max:50',

            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',

        ]);

        if ($request->hasFile('image')) {

            if (
                $artwork->image_url &&
                !str_starts_with($artwork->image_url, 'http')
            ) {

                Storage::disk('public')->delete($artwork->image_url);
            }

            $validated['image_url'] = $request
                ->file('image')
                ->store('artworks', 'public');
        }

        $artwork->update($validated);

        return redirect()
            ->route('admin.artworks.index')
            ->with('success', 'Artwork updated successfully.');
    }

    public function destroy(Artwork $artwork)
    {
        if ($artwork->image_url && !str_starts_with($artwork->image_url, 'http')) {
            Storage::disk('public')->delete($artwork->image_url);
        }
        $artwork->delete();
        return redirect()->route('admin.artworks.index')->with('success', 'Artwork deleted successfully.');
    }


}

