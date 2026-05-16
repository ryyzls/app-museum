<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArtworkController;
use App\Http\Controllers\ExhibitionController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TransactionController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/tickets/{ticket}/checkout', [TransactionController::class, 'checkout'])
    ->name('tickets.checkout');

Route::post('/tickets/{ticket}/reserve', [TransactionController::class, 'reserve'])
    ->name('tickets.reserve');

Route::get('/transactions/{transaction}/success', [TransactionController::class, 'success'])
    ->name('transactions.success');

Route::get(
    '/transactions/{transaction}/download-ticket',
    [TransactionController::class, 'downloadTicket']
)->name('transactions.download-ticket');

Route::resource('artworks', ArtworkController::class);
Route::resource('exhibitions', ExhibitionController::class);
Route::resource('tickets', TicketController::class);
Route::resource('transactions', TransactionController::class);