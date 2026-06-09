<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Artwork;
use App\Models\Artist;
use App\Models\Exhibition;
use App\Models\Ticket;
use App\Models\Transaction;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalArtworks = \App\Models\Artwork::count();
        $totalArtists = \App\Models\Artist::count();
        $totalExhibitions = \App\Models\Exhibition::count();
        $totalTickets = \App\Models\Ticket::count();
        $totalTransactions = \App\Models\Transaction::count();

        // MENGAMBIL DATA DARI SQL VIEW UNTUK GRAFIK
        $chartData = \Illuminate\Support\Facades\DB::table('vw_exhibition_performance')->get();
        $recentTransactions = Transaction::with([
            'user',
            'ticket.exhibition'
        ])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalArtworks',
            'totalArtists',
            'totalExhibitions',
            'totalTickets',
            'totalTransactions',
            'chartData',
            'recentTransactions'
        ));
    }
}

