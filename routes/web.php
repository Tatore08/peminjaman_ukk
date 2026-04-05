<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\AlatController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\PengembalianController;  // ← TAMBAH INI
use App\Http\Controllers\LogAktivitasController;
use App\Http\Controllers\LaporanController;

/*
|--------------------------------------------------------------------------
| ROOT
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| GUEST
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

/*
|--------------------------------------------------------------------------
| AUTHENTICATED
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | ADMIN ONLY
    |--------------------------------------------------------------------------
    */
    Route::middleware(['checkrole:admin'])->group(function () {

        // USERS ✅ DATABASE
        Route::resource('users', UserController::class)->except(['show', 'create', 'edit']);

        // KATEGORI ✅ DATABASE
        Route::resource('kategori', KategoriController::class);

        // PEMINJAMAN - Delete ✅ DATABASE
        Route::delete('/peminjaman/{id}', [PeminjamanController::class, 'destroy'])->name('peminjaman.destroy');

        // PENGEMBALIAN - Delete ✅ DATABASE
        Route::delete('/pengembalian/{id}', [PengembalianController::class, 'destroy'])->name('pengembalian.destroy');

        // LOG AKTIVITAS ← UPDATE YANG INI
        Route::get('/log-aktivitas', [LogAktivitasController::class, 'index'])->name('log.index');
        Route::post('/log-aktivitas/clear', [LogAktivitasController::class, 'clear'])->name('log.clear');

        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/rekap', [LaporanController::class, 'rekapPeriode'])->name('laporan.rekap');
        Route::get('/laporan/cetak-rekap', [LaporanController::class, 'cetakRekap'])->name('laporan.cetak-rekap');
        Route::get('/laporan/peminjaman/{id}', [LaporanController::class, 'cetakPeminjaman'])->name('laporan.peminjaman');
        Route::get('/laporan/pengembalian/{id}', [LaporanController::class, 'cetakPengembalian'])->name('laporan.pengembalian');

    });

    /*
    |--------------------------------------------------------------------------
    | VIEW DATA - SEMUA ROLE BISA LIHAT
    |--------------------------------------------------------------------------
    */
    Route::middleware(['checkrole:admin,petugas,peminjam'])->group(function () {

        // ALAT ✅ DATABASE
        Route::get('/alat', [AlatController::class, 'index'])->name('alat.index');

        // PEMINJAMAN ✅ DATABASE
        Route::get('/peminjaman', [PeminjamanController::class, 'index'])->name('peminjaman.index');
        Route::post('/peminjaman', [PeminjamanController::class, 'store'])->name('peminjaman.store');

        // PENGEMBALIAN ✅ DATABASE
        Route::get('/pengembalian', [PengembalianController::class, 'index'])->name('pengembalian.index');
        Route::post('/pengembalian', [PengembalianController::class, 'store'])->name('pengembalian.store');

        // Approve & Reject (Petugas & Admin only)
        Route::post('/pengembalian/{id}/approve', [PengembalianController::class, 'approve'])
            ->name('pengembalian.approve')
            ->middleware('checkrole:admin,petugas');
        
        Route::post('/pengembalian/{id}/reject', [PengembalianController::class, 'reject'])
            ->name('pengembalian.reject')
            ->middleware('checkrole:admin,petugas');
        
        // Delete (Admin only)
        Route::delete('/pengembalian/{id}', [PengembalianController::class, 'destroy'])
            ->name('pengembalian.destroy')
            ->middleware('checkrole:admin');
    });

    /*
    |--------------------------------------------------------------------------
    | CRUD DATA - ADMIN & PETUGAS
    |--------------------------------------------------------------------------
    */
    Route::middleware(['checkrole:admin,petugas'])->group(function () {

        // ALAT ✅ DATABASE
        Route::post('/alat', [AlatController::class, 'store'])->name('alat.store');
        Route::put('/alat/{id}', [AlatController::class, 'update'])->name('alat.update');
        Route::delete('/alat/{id}', [AlatController::class, 'destroy'])->name('alat.destroy');

        // PEMINJAMAN - Approve & Reject ✅ DATABASE
        Route::post('/peminjaman/{id}/approve', [PeminjamanController::class, 'approve'])->name('peminjaman.approve');
        Route::post('/peminjaman/{id}/reject', [PeminjamanController::class, 'reject'])->name('peminjaman.reject');
    });

    /*
    |--------------------------------------------------------------------------
    | PEMINJAM ACTIONS
    |--------------------------------------------------------------------------
    */
    Route::middleware(['checkrole:peminjam'])->group(function () {
        // PEMINJAMAN - Cancel ✅ DATABASE
        Route::post('/peminjaman/{id}/cancel', [PeminjamanController::class, 'cancel'])->name('peminjaman.cancel');
    });
});