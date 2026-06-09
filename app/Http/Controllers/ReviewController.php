<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    /**
     * Menyimpan data ulasan bintang dan komentar dari modal.
     */
    public function store(Request $request, $id)
    {
        // 1. Validasi data yang masuk dari form modal
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        // 2. Simpan data langsung ke tabel reviews menggunakan Query Builder
        // Pastikan nama tabel di database kamu adalah 'reviews'
        DB::table('reviews')->insert([
            'artwork_id' => $id,
            'user_id' => auth()->id() ?? null, // Mengisi ID user jika sistem login aktif
            'rating' => $request->rating,
            'comment' => $request->comment,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Kembali ke halaman reviews dengan membawa pesan sukses
        return redirect()->back()->with('success', 'Penilaian berhasil dikirim!');
    }
}