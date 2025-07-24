<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Panel;
use Filament\Facades\Filament;

class FilamentCustomizationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Filament::serving(function () {
            Filament::registerRenderHook(
                'panels::head.end',
                fn(): string => '<link rel="icon" type="image/png" href="' . asset('assets/images/logo/logo.png') . '">'
            );
        });
    }
}
