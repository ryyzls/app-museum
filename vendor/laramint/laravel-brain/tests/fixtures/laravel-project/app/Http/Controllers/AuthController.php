<?php

namespace App\Http\Controllers;

use App\Events\UserLoggedIn;
use App\Models\User;
use App\Services\AuthService;

class AuthController
{
    public function __construct(
        private AuthService $authService,
    ) {}

    public function login()
    {
        $user = User::where('email', 'test@example.com')->first();
        $token = $this->authService->generateToken($user);
        event(new UserLoggedIn($user));

        return response()->json(['token' => $token]);
    }
}
