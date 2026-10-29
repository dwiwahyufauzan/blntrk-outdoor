<?php

namespace App\Providers;

use App\View\Components\AdminLayout;
use App\View\Components\PemilikLayout;
use App\View\Components\PublikLayout;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::component('layouts.publik', PublikLayout::class);
        Blade::component('layouts.admin', AdminLayout::class);
        Blade::component('layouts.pemilik', PemilikLayout::class);
        Blade::component('publik-layout', PublikLayout::class);
        Blade::component('admin-layout', AdminLayout::class);
        Blade::component('pemilik-layout', PemilikLayout::class);
    }
}
