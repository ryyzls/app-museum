<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::withCount([
            'transactions',
            'reviews'
        ])
            ->latest()
            ->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load([
            'transactions.ticket.exhibition',
            'reviews.artwork'
        ]);

        return view('admin.users.show', compact('user'));
    }
}