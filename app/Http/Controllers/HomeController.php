<?php

namespace App\Http\Controllers;

use App\Models\Exhibition;
use App\Models\Artwork;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | FEATURED EXHIBITION (Highlight Utama)
        |--------------------------------------------------------------------------
        */
        $featuredExhibition = Exhibition::with(['museum', 'artworks'])
            ->latest()
            ->first();

        /*
        |--------------------------------------------------------------------------
        | LATEST EXHIBITIONS (3 exhibitions terbaru)
        |--------------------------------------------------------------------------
        */
        $latestExhibitions = Exhibition::with(['museum', 'artworks'])
            ->latest()
            ->take(3)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | FEATURED ARTWORKS (Untuk section artworks)
        |--------------------------------------------------------------------------
        */
        $featuredArtworks = Artwork::with(['artist', 'category', 'museum'])
            ->whereNotNull('image_url')
            ->whereNotNull('description')
            ->whereRaw('TRIM(description) != ""')
            ->whereRaw('LENGTH(description) > 15')
            ->inRandomOrder()
            ->take(6)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | INSTAGRAM-STYLE POSTS (Random artworks untuk gallery)
        |--------------------------------------------------------------------------
        */
        $instagramPosts = Artwork::with(['artist'])
            ->whereNotNull('image_url')
            ->inRandomOrder()
            ->take(9)
            ->get();

        return view('welcome', compact(
            'featuredExhibition',
            'latestExhibitions',
            'featuredArtworks',
            'instagramPosts'
        ));
    }
}