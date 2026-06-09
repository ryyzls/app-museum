<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Exhibition;
use App\Models\Museum;
use Illuminate\Http\Request;
use App\Models\Artwork;
use Illuminate\Support\Facades\Storage;

class AdminExhibitionController extends Controller
{
    public function index(Request $request)
    {
        $query = Exhibition::with([
            'museum',
            'artworks'
        ]);

        /*
        |--------------------------------------------------------------------------
        | SEARCH EXHIBITION
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where('title', 'like', "%{$search}%");

        }

        $exhibitions = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.exhibitions.index', compact(
            'exhibitions'
        ));
    }



    public function create()
    {
        $museums = Museum::orderBy('name')->get();
        $artworks = Artwork::with('artist', 'category')->orderBy('title')->get();
        $categories = \App\Models\Category::orderBy('name')->get();
        return view('admin.exhibitions.create', compact('museums', 'artworks', 'categories'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([

            'title' => 'required|max:255',

            'subtitle' => 'nullable|max:255',

            'description' => 'required',

            'museum_id' => 'required',

            'start_date' => 'required|date',

            'end_date' => 'required|date|after:start_date',

            'banner' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',

            'artworks' => 'nullable|array',
            'artworks.*' => 'exists:artworks,id',

        ]);

        if ($request->hasFile('banner')) {

            $validated['banner_image'] = $request
                ->file('banner')
                ->store('exhibitions', 'public');
        }

        $exhibition = Exhibition::create($validated);

        if ($request->filled('artworks')) {
            $exhibition->artworks()->sync($request->artworks);
        }

        return redirect()
            ->route('admin.exhibitions.index')
            ->with('success', 'Exhibition created successfully.');
    }

    public function edit(Exhibition $exhibition)
    {
        $museums = Museum::orderBy('name')->get();
        $categories = \App\Models\Category::orderBy('name')->get();
        $artworks = Artwork::with('artist', 'category')->orderBy('title')->get();
        return view('admin.exhibitions.edit', compact('exhibition', 'museums', 'categories', 'artworks'));
    }

    public function update(Request $request, Exhibition $exhibition)
    {
        $validated = $request->validate(['title' => 'required|max:255', 'subtitle' => 'nullable|max:255', 'description' => 'required', 'museum_id' => 'required', 'start_date' => 'required|date', 'end_date' => 'required|date|after:start_date', 'status' => 'required', 'artworks' => 'nullable|array', 'artworks.*' => 'exists:artworks,id', 'banner' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',]);
        if ($request->hasFile('banner')) {
            if ($exhibition->banner_image && !str_starts_with($exhibition->banner_image, 'http')) {
                Storage::disk('public')->delete($exhibition->banner_image);
            }
            $validated['banner_image'] = $request->file('banner')->store('exhibitions', 'public');
        }
        $exhibition->update($validated);
        $exhibition->artworks()->sync($request->artworks ?? []);
        return redirect()->route('admin.exhibitions.index')->with('success', 'Exhibition updated successfully.');
    }
    public function destroy(Exhibition $exhibition)
    {
        if ($exhibition->banner_image && !str_starts_with($exhibition->banner_image, 'http')) {
            Storage::disk('public')->delete($exhibition->banner_image);
        }
        $exhibition->delete();
        return redirect()->route('admin.exhibitions.index')->with('success', 'Exhibition deleted successfully.');
    }

}

