<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class UserController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            // Applies to every action
            'auth',

            // Only for index and show
            new Middleware('log', only: ['index', 'show']),

            // All except index
            new Middleware('subscribed', except: ['index']),
        ];
    }

    public function index() {}

    public function show() {}

    public function store() {}
}
