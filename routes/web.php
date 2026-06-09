<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ArtworkController;
use App\Http\Controllers\ExhibitionController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ArtworkReviewController;

// Admin Controllers
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminArtworkController;
use App\Http\Controllers\Admin\AdminExhibitionController;
use App\Http\Controllers\Admin\AdminTicketController;
use App\Http\Controllers\Admin\AdminArtistController;
use App\Http\Controllers\Admin\AdminTransactionLogController;
use App\Http\Controllers\Admin\AdminRevenueReportController;
use App\Http\Controllers\Admin\AdminUserController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

// Menggunakan WelcomeController agar halaman depan memanggil database secara dinamis
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/about', [CommentController::class, 'index']);
Route::get('/contact', function () {
    return view('contact');
});

Route::get('/reviews', [ArtworkReviewController::class, 'index'])
    ->name('reviews.index');

Route::post('/reviews/{id}', [ReviewController::class, 'store'])
    ->middleware('auth')
    ->name('reviews.store');

Route::resource('artworks', ArtworkController::class);
Route::resource('exhibitions', ExhibitionController::class);
Route::resource('tickets', TicketController::class);
Route::resource('transactions', TransactionController::class);

Route::post('/comment/store', [CommentController::class, 'store'])->middleware('auth');
Route::put('/comment/{id}', [CommentController::class, 'update'])->middleware('auth');
Route::delete('/comment/{id}', [CommentController::class, 'destroy'])->middleware('auth');

/*
|--------------------------------------------------------------------------
| AUTHENTICATED USER ROUTES (PENGGUNA LOG-IN)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/checkout', [TransactionController::class, 'checkout'])->name('checkout.show');
    Route::post('/checkout/{ticket}', [TransactionController::class, 'store'])->name('checkout.process');
    Route::get('/invoice/{transaction_code}', [TransactionController::class, 'show'])->name('transactions.show');
    Route::get('/ticket/download/{transaction}', [TransactionController::class, 'downloadTicket'])->name('tickets.download');

    Route::get('/tickets/{ticket}/checkout', [TransactionController::class, 'checkout'])->name('tickets.checkout');
    Route::post('/tickets/{ticket}/reserve', [TransactionController::class, 'reserve'])->name('tickets.reserve');
    Route::get('/transactions/{transaction}/success', [TransactionController::class, 'success'])->name('transactions.success');
    Route::get('/transactions/{transaction}/download-ticket', [TransactionController::class, 'downloadTicket'])->name('transactions.download-ticket');

});

/*
|--------------------------------------------------------------------------
| ADMIN AUTHENTICATED ROUTES (KHUSUS ADMIN)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/transaction-logs', [AdminTransactionLogController::class, 'index'])
        ->name('transaction-logs.index');
    Route::get('/revenue-report', [AdminRevenueReportController::class, 'index'])
        ->name('revenue-report.index');

    Route::resource('artworks', AdminArtworkController::class);
    Route::resource('exhibitions', AdminExhibitionController::class);
    Route::resource('tickets', AdminTicketController::class);
    Route::resource('artists', AdminArtistController::class);

    Route::get('/users', [AdminUserController::class, 'index'])
        ->name('users.index');

    Route::get('/users/{user}', [AdminUserController::class, 'show'])
        ->name('users.show');

});



require __DIR__ . '/auth.php';