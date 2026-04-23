<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

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
        Gate::define('viewApiDocs', function ($user) {
            return $user->hasRole('super_admin') || $user->can('View:ApiDocs');
        });
        Scramble::configure()
            ->withDocumentTransformers(function (OpenApi $openApi) {
                $openApi->secure(
                    SecurityScheme::http('bearer')
                );
            });

        Filament::serving(function () {
            Filament::registerNavigationItems([
                NavigationItem::make('Documentación - API')
                    ->url('/api/documentation')
                    ->icon('heroicon-o-book-open')
                    ->group('Sistema')
                    ->sort(99) // para que aparezca al final del grupo
                    ->visible(fn() => Auth::user()->can('View:ApiDocs')),
            ]);
        });
    }
}
