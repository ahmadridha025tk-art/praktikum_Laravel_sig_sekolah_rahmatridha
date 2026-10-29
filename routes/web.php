<?php

use App\Http\Controllers\PetaController;
use App\Http\Controllers\SekolahController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PetaController::class, 'index'])->name('peta.index');

Route::get('/data/sekolah', [PetaController::class, 'geojson'])
    ->name('peta.data');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('sekolah', SekolahController::class)->except('show');
});
