<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileStoreRequest;

class ProfileController extends Controller
{
    public function __construct()
    {
        // Applies to every action
        $this->middleware('auth');

        // Only for store
        $this->middleware('verified', ['only' => ['store']]);

        // Fluent chain — all except destroy
        $this->middleware('log')->except(['destroy']);
    }

    public function index() {}

    public function store(ProfileStoreRequest $request) {}

    public function destroy() {}
}
