<?php

namespace App\Providers;

use Termwind\Enums\Color;
use Filament\Facades\Filament;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentView;
use Filament\Support\Facades\FilamentColor;

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
        Model::unguard();
        FilamentColor::register([
            'medium_gray' => Color::GRAY_300,
        ]);
        FilamentView::registerRenderHook(
            PanelsRenderHook::TOPBAR_START,
            fn (): string => Blade::render('
            <div class="hidden lg:flex items-center space-x-2">
                <p>Signed in as: <span class="font-bold text-primary-500">{{ $user->name }}</span> | <span class="font-bold text-primary-500">{{ $user->email }}</span></p>
            </div>
        ', ['user' => Filament::auth()->user()])
        );
        FilamentView::registerRenderHook(
            'panels::user-menu.before',
            fn (): string => '', // Remove theme switcher
        );
    }
}
