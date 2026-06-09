<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artwork;
use App\Models\Artist;
use App\Models\Category;
use App\Models\Museum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;

class AdminArtworkController extends Controller
{
    public function index(Request $request)
    {
        // 1. Mulai Query dengan Eager Loading
        $query = Artwork::with([
            'artist',
            'category',
            'museum'
        ]);

        // 2. Fitur Pencarian (Search)
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('ark_id', 'like', '%' . $request->search . '%');
            });
        }

        // 3. Fitur Filter Kategori
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // 4. Ambil Data Artwork dengan Pagination
        $artworks = $query->latest()
            ->paginate(12)
            ->withQueryString();

        // 5. AMBIL DATA KATEGORI
        $categories = Category::orderBy('name')->get();

        // 6. Lempar kedua variabel ke View
        return view('admin.artworks.index', compact('artworks', 'categories'));
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
        $validated = $request->validate([
            'title' => 'required|max:255',
            'artist_id' => 'required',
            'category_id' => 'required',
            'museum_id' => 'required',
            'description' => 'nullable',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096'
        ]);

        if ($request->hasFile('image')) {
            $validated['image_url'] = $request->file('image')->store('artworks', 'public');
        }

        // GENERATE ARK ID
        $validated['ark_id'] = 'ARK-' . strtoupper(Str::random(8));

        Artwork::create($validated);

        // Panggil SweetAlert sebelum redirect
        Alert::success('Success!', 'Artwork created successfully.');

        return redirect()->route('admin.artworks.index');
    }

    public function edit(Artwork $artwork)
    {
        $artists = Artist::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $museums = Museum::orderBy('name')->get();

        return view('admin.artworks.edit', compact('artwork', 'artists', 'categories', 'museums'));
    }

    public function update(Request $request, Artwork $artwork)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'artist_id' => 'required',
            'category_id' => 'required',
            'museum_id' => 'required',
            'description' => 'nullable',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        if ($request->hasFile('image')) {
            if ($artwork->image_url && !str_starts_with($artwork->image_url, 'http')) {
                Storage::disk('public')->delete($artwork->image_url);
            }
            $validated['image_url'] = $request->file('image')->store('artworks', 'public');
        }

        $artwork->update($validated);

        // Panggil SweetAlert sebelum redirect
        Alert::success('Updated!', 'Artwork updated successfully.');

        return redirect()->route('admin.artworks.index');
    }

    public function destroy(Artwork $artwork)
    {
        if ($artwork->image_url && !str_starts_with($artwork->image_url, 'http')) {
            Storage::disk('public')->delete($artwork->image_url);
        }
        $artwork->delete();

        // Panggil SweetAlert sebelum redirect
        Alert::success('Deleted!', 'Artwork deleted successfully.');

        return redirect()->route('admin.artworks.index');
    }
}