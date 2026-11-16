<?php

use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;

// Halaman Utama & Daftar Event Publik (Melihat dan Mencari Event)
Route::get('/', [EventController::class, 'index'])->name('home');
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');

// Area Pengguna Terautentikasi (Peserta & Admin)
Route::middleware('auth')->group(function () {
    // Dashboard Pengguna
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Pendaftaran Event (Dilindungi DB Transaction + Pessimistic Lock)
    Route::post('/events/{event}/register', [RegistrationController::class, 'store'])->name('events.register');

    // Manajemen Tiket Peserta (Dilindungi Policy Anti-IDOR)
    Route::get('/my-tickets', [RegistrationController::class, 'index'])->name('registrations.index');
    Route::get('/tickets/{registration}', [RegistrationController::class, 'show'])->name('registrations.show');
    Route::delete('/tickets/{registration}', [RegistrationController::class, 'destroy'])->name('registrations.destroy');

    // Profil Pengguna
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

    // Pemindaian & Verifikasi Kehadiran Tiket QR (Rate limited: anti brute-force token)
    Route::get('/scan', [\App\Http\Controllers\Admin\ScanController::class, 'index'])->name('scan.index');
    Route::post('/scan/verify', [\App\Http\Controllers\Admin\ScanController::class, 'verify'])
        ->middleware('throttle:scan')
        ->name('scan.verify');

    // Laporan Kehadiran & Ekspor CSV Aman
    Route::get('/reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/{event}', [\App\Http\Controllers\Admin\ReportController::class, 'show'])->name('reports.show');
    Route::get('/reports/{event}/export-csv', [\App\Http\Controllers\Admin\ReportController::class, 'exportCsv'])->name('reports.export');

    // Log Aktivitas Admin
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
});

require __DIR__.'/auth.php';
