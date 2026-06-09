<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\V3\ThingV3Controller;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
});

Route::prefix('admin')->middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::delete('/orders/{id}', [OrderController::class, 'destroy']);
});

// Post-route chaining: ->middleware() after the HTTP method call
Route::get('/brands', [OrderController::class, 'index'])
    ->middleware('ability:view-maintenance-requests,monitor-maintenance,create-transfer');

// Controller-level middleware via HasMiddleware
Route::get('/users', [UserController::class, 'index']);
Route::get('/users/{id}', [UserController::class, 'show']);
Route::post('/users', [UserController::class, 'store']);

// Controller-level middleware via $this->middleware() in __construct
Route::get('/profile', [ProfileController::class, 'index']);
Route::post('/profile', [ProfileController::class, 'store']);
Route::delete('/profile', [ProfileController::class, 'destroy']);

Route::get('/v3/things', [ThingV3Controller::class, 'index']);
