<?php

use App\Http\Controllers\PetaController;
use App\Http\Controllers\SekolahController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

Route::get('/', [PetaController::class, 'index'])->name('peta.index');

Route::get('/data/sekolah', [PetaController::class, 'geojson'])
    ->name('peta.data');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('sekolah', SekolahController::class)->except('show');
});

// Tamu
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
});

// Pengguna yang sudah login
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
});
