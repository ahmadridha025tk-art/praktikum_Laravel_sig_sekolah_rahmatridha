<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Sekolah;
use App\Observers\SekolahObserver;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Sekolah::observe(SekolahObserver::class);
    }
}