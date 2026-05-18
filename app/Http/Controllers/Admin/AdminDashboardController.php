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
        $totalArtworks = Artwork::count();

        $totalArtists = Artist::count();

        $totalExhibitions = Exhibition::count();

        $totalTickets = Ticket::count();

        $totalTransactions = Transaction::count();

        return view('admin.dashboard', compact(

            'totalArtworks',
            'totalArtists',
            'totalExhibitions',
            'totalTickets',
            'totalTransactions'

        ));
    }
}

