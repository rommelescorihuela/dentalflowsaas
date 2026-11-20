<?php

namespace App\Providers\Filament;

use App\Filament\App\Pages\Dashboard;
use App\Helpers\ClinicHelper;
use App\Http\Middleware\EnsureSubscriptionActive;
use App\Http\Middleware\InitializeTenancyBySubdomainId;
use App\Http\Middleware\SetTenancyUrlDefaults;
use App\Http\Middleware\SyncSpatiePermissionsTeamId;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AppPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $host = request()->getHost();
        $isLocalDev = in_array($host, ['localhost', '127.0.0.1']);

        return $panel
            ->id('app')
            ->path('app')
            ->viteTheme('resources/css/filament/app/theme.css')
            ->login()
            ->brandName('DentalFlow')
            ->brandLogo(function () {
                $logo = ClinicHelper::getLogo();

                return $logo ? asset('storage/'.$logo) : \App\Support\BrandLogo::html();
            })
            ->brandLogoHeight('2.25rem')
            ->favicon(asset('images/favicon.svg'))
            ->font('Outfit')
            ->darkMode(true)
            ->defaultThemeMode(\Filament\Enums\ThemeMode::Light)
            ->spa()
            ->maxContentWidth(\Filament\Support\Enums\Width::Full)
            ->colors([
                'primary' => Color::hex('#1f9e8f'),
            ])
            ->sidebarCollapsibleOnDesktop()
            ->discoverResources(in: app_path('Filament/App/Resources'), for: 'App\Filament\App\Resources')
            ->discoverPages(in: app_path('Filament/App/Pages'), for: 'App\Filament\App\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/App/Widgets'), for: 'App\Filament\App\Widgets')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                InitializeTenancyBySubdomainId::class,
                SetTenancyUrlDefaults::class,
                SyncSpatiePermissionsTeamId::class,
            ])
            ->plugins([
                // Shield removed - using custom role management
            ])
            ->authMiddleware([
                Authenticate::class,
                EnsureSubscriptionActive::class,
            ]);
    }
}
