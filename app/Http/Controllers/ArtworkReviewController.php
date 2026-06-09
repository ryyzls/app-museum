<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArtworkReviewController extends Controller
{
    public function index()
    {
        // Mengambil 15 artwork secara acak setiap kali halaman dikunjungi
        // Eager load 'reviews' untuk mengecek apakah user sudah pernah rating
        $artworks = Artwork::with('reviews')->inRandomOrder()->limit(15)->get();

        return view('reviews', compact('artworks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'artwork_id' => 'required|exists:artworks,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        // Cek kembali di sisi server untuk memastikan aturan 1 user = 1 rating per artwork
        $alreadyReviewed = Review::where('user_id', Auth::id())
            ->where('artwork_id', $request->artwork_id)
            ->exists();

        if ($alreadyReviewed) {
            return redirect()->back()->with('error', 'Kamu sudah memberikan rating untuk karya ini!');
        }

        Review::create([
            'user_id' => Auth::id(),
            'artwork_id' => $request->artwork_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return redirect()->back()->with('success', 'Review berhasil disimpan. Terima kasih!');
    }
}