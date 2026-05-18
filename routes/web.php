<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArtworkController;
use App\Http\Controllers\ExhibitionController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminArtworkController;

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
)
    ->name('transactions.download-ticket');



Route::prefix('admin')->group(function () {

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])
        ->name('admin.dashboard');

    Route::get('/artworks', [AdminArtworkController::class, 'index'])
        ->name('admin.artworks.index');

    Route::get('/artworks/create', [AdminArtworkController::class, 'create'])->name('admin.artworks.create');
    Route::post('/artworks', [AdminArtworkController::class, 'store'])->name('admin.artworks.store');

    Route::get('/artworks/{artwork}/edit', [AdminArtworkController::class, 'edit'])->name('admin.artworks.edit');
    Route::put('/artworks/{artwork}', [AdminArtworkController::class, 'update'])->name('admin.artworks.update');
    Route::delete('/artworks/{artwork}', [AdminArtworkController::class, 'destroy']) ->name('admin.artworks.destroy');

});



Route::resource('artworks', ArtworkController::class);
Route::resource('exhibitions', ExhibitionController::class);
Route::resource('tickets', TicketController::class);
Route::resource('transactions', TransactionController::class);