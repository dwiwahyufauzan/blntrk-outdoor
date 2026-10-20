<?php

namespace App\Providers;

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
        \Illuminate\Support\Facades\Blade::component('layouts.publik', \App\View\Components\PublikLayout::class);
        \Illuminate\Support\Facades\Blade::component('layouts.admin', \App\View\Components\AdminLayout::class);
        \Illuminate\Support\Facades\Blade::component('layouts.pemilik', \App\View\Components\PemilikLayout::class);
        \Illuminate\Support\Facades\Blade::component('publik-layout', \App\View\Components\PublikLayout::class);
        \Illuminate\Support\Facades\Blade::component('admin-layout', \App\View\Components\AdminLayout::class);
        \Illuminate\Support\Facades\Blade::component('pemilik-layout', \App\View\Components\PemilikLayout::class);
    }
}
