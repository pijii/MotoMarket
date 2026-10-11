<?php

use App\Http\Controllers\ConversationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\MotorcycleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Seller\DashboardController as SellerDashboardController;
use App\Http\Controllers\Seller\MotorcycleController as SellerMotorcycleController;
use App\Http\Controllers\Seller\SellerProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MotorcycleController::class, 'home'])->name('home');
Route::get('/motorcycles', [MotorcycleController::class, 'index'])->name('motorcycles.index');
Route::get('/motorcycles/{motorcycle}', [MotorcycleController::class, 'show'])->name('motorcycles.show');

Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/motorcycles/{motorcycle}/favorite', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
    Route::post('/motorcycles/{motorcycle}/report', [ReportController::class, 'store'])->middleware('throttle:5,1')->name('reports.store');

    Route::get('/sell/register', [SellerProfileController::class, 'registerForm'])->middleware('role:buyer')->name('seller.register');
    Route::post('/sell/register', [SellerProfileController::class, 'register'])->middleware('role:buyer')->name('seller.register.store');

    Route::get('/conversations', [ConversationController::class, 'index'])->name('conversations.index');
    Route::post('/motorcycles/{motorcycle}/contact', [ConversationController::class, 'start'])->middleware('throttle:10,1')->name('conversations.start');
    Route::get('/conversations/{conversation}', [ConversationController::class, 'show'])->name('conversations.show');
    Route::post('/conversations/{conversation}/messages', [ConversationController::class, 'store'])->middleware('throttle:10,1')->name('conversations.messages.store');

    Route::prefix('seller')->name('seller.')->middleware('role:seller')->group(function () {
        Route::get('/dashboard', SellerDashboardController::class)->name('dashboard');
        Route::get('/motorcycles', [SellerMotorcycleController::class, 'index'])->name('motorcycles.index');
        Route::get('/motorcycles/create', [SellerMotorcycleController::class, 'create'])->name('motorcycles.create');
        Route::post('/motorcycles', [SellerMotorcycleController::class, 'store'])->name('motorcycles.store');
        Route::get('/motorcycles/{motorcycle}/edit', [SellerMotorcycleController::class, 'edit'])->name('motorcycles.edit');
        Route::put('/motorcycles/{motorcycle}', [SellerMotorcycleController::class, 'update'])->name('motorcycles.update');
        Route::delete('/motorcycles/{motorcycle}', [SellerMotorcycleController::class, 'destroy'])->name('motorcycles.destroy');
        Route::post('/motorcycles/{motorcycle}/sold', [SellerMotorcycleController::class, 'markSold'])->name('motorcycles.sold');
        Route::get('/profile', [SellerProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [SellerProfileController::class, 'update'])->name('profile.update');
    });

    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/motorcycles', [AdminController::class, 'motorcycles'])->name('motorcycles.index');
        Route::post('/motorcycles/{motorcycle}/approve', [AdminController::class, 'approve'])->name('motorcycles.approve');
        Route::post('/motorcycles/{motorcycle}/reject', [AdminController::class, 'reject'])->name('motorcycles.reject');
        Route::delete('/motorcycles/{motorcycle}', [AdminController::class, 'remove'])->name('motorcycles.remove');
        Route::get('/users', [AdminController::class, 'users'])->name('users.index');
        Route::patch('/users/{user}/role', [AdminController::class, 'updateRole'])->name('users.role');
        Route::patch('/users/{user}/status', [AdminController::class, 'updateStatus'])->name('users.status');
        Route::get('/reports', [AdminController::class, 'reports'])->name('reports.index');
        Route::get('/reports/export', [AdminController::class, 'exportReports'])->name('reports.export');
        Route::patch('/reports/{report}', [AdminController::class, 'updateReport'])->name('reports.update');
    });
});

Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
