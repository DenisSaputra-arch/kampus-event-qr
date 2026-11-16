<?php

use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Halaman Utama & Daftar Event Publik (Melihat dan Mencari Event)
Route::get('/', [EventController::class, 'index'])->name('home');
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');

// Dashboard Peserta
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Profil Pengguna (auth)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Area Khusus Administrator
// Keamanan: Dilindungi middleware 'auth' (wajib login) dan 'role:admin' (RBAC)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // CRUD Event oleh Admin
    Route::resource('events', AdminEventController::class);
});

require __DIR__.'/auth.php';
