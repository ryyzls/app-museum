<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArtworkController;
use App\Http\Controllers\ExhibitionController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TransactionController;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('artworks', ArtworkController::class);
Route::resource('exhibitions', ExhibitionController::class);
Route::resource('tickets', TicketController::class);
Route::resource('transactions', TransactionController::class);