<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PetaController;
use App\Http\Controllers\SekolahController;
use App\Http\Controllers\ProfilSekolahController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PasswordController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicProfilSekolahController;

// ==========================================
// PUBLIK
// ==========================================

Route::controller(PetaController::class)->group(function () {

    Route::get('/', 'index')->name('peta.index');

    Route::get('/data/sekolah', 'geojson')->name('peta.data');

       // Menu Pencarian
    Route::get('/pencarian', 'cari')->name('peta.cari');

    // Menu Klaster
    Route::get('/klaster', 'klaster')->name('peta.klaster');

    // Menu Peta Panas
    Route::get('/peta-panas', 'panas')->name('peta.panas');

    // Menu Rute
    Route::get('/rute', 'rute')->name('peta.rute');

    // Menu Cek Lokasi
    Route::get('/cek-alamat', 'alamat')->name('peta.alamat');
    });

      
// ==========================================
// MENU WILAYAH
// ==========================================

Route::get('/wilayah', [
    PetaController::class,
    'wilayah'
])->name('peta.wilayah');

Route::get('/data/wilayah', [
    PetaController::class,
    'geojsonWilayah'
])->name('peta.data.wilayah');


 // Halaman profil publik sekolah
Route::get('/profil-sekolah/{sekolah}', [
    PublicProfilSekolahController::class,
    'show'
])->name('sekolah.profil.publik');   


// ==========================================
// TAMU
// ==========================================

Route::middleware('guest')->group(function () {

    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1');

});


// ==========================================
// PENGGUNA YANG SUDAH LOGIN
// ==========================================

Route::middleware('auth')->group(function () {

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

    // Dashboard
    Route::get('/dashboard', DashboardController::class)
        ->name('dashboard');


    // ======================================
    // UBAH KATA SANDI
    // ======================================

    Route::get('/ubah-password', [PasswordController::class, 'edit'])
        ->name('password.edit');

    Route::put('/ubah-password', [PasswordController::class, 'update'])
        ->name('password.update');


    // ======================================
    // DINAS: KELOLA DATA SEKOLAH
    // ======================================

    Route::middleware('role:dinas')
        ->prefix('dinas')
        ->name('dinas.')
        ->group(function () {

            Route::resource('sekolah', SekolahController::class)
                ->only([
                    'index',
                    'create',
                    'store',
                    'destroy'
                ]);

        });


    // ======================================
    // DINAS DAN SEKOLAH: UBAH DATA SEKOLAH
    // ======================================

    Route::middleware('role:dinas,sekolah')->group(function () {

        // Membuka halaman edit sekolah
        Route::get('/sekolah/{sekolah}/edit', [SekolahController::class, 'edit'])
            ->name('sekolah.edit');

        // Menyimpan perubahan data sekolah
        Route::put('/sekolah/{sekolah}', [SekolahController::class, 'update'])
            ->name('sekolah.update');


        // ==================================
        // PROFIL SEKOLAH
        // ==================================

        Route::get('/sekolah/{sekolah}/profil', [ProfilSekolahController::class, 'edit'])
            ->name('sekolah.profil.edit');

        Route::put('/sekolah/{sekolah}/profil', [ProfilSekolahController::class, 'update'])
            ->name('sekolah.profil.update');

    });


    // ======================================
    // ADMIN: KELOLA PENGGUNA
    // ======================================

    Route::middleware('role:admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

            // CRUD pengguna
            Route::resource('users', UserController::class)
                ->except('show');

            // Aktifkan / Nonaktifkan akun
            Route::patch(
                'users/{user}/toggle-status',
                [UserController::class, 'toggleActive']
            )->name('users.toggleActive');

        });

});
