<?php

namespace App\Providers;

use App\View\Components\AdminLayout;
use App\View\Components\PemilikLayout;
use App\View\Components\PublikLayout;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
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

        // Macro adapter agar Collection kompatibel jika view pagination lama dipanggil
        Collection::macro('firstItem', function () {
            /** @var Collection $this */
            return $this->isEmpty() ? 0 : 1;
        });
        Collection::macro('lastItem', function () {
            /** @var Collection $this */
            return $this->count();
        });
        Collection::macro('total', function () {
            /** @var Collection $this */
            return $this->count();
        });
        Collection::macro('links', function () {
            return new HtmlString('');
        });
    }
}
