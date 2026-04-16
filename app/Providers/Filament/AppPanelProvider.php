<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

use App\Models\Clinic;

class AppPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $host = request()->getHost();
        $isCentral = in_array($host, config('tenancy.central_domains', []));
        
        \Illuminate\Support\Facades\Log::info("Tenancy Debug: Host: {$host}, IsCentral: " . ($isCentral ? 'Yes' : 'No') . ", Config: " . json_encode(config('tenancy.central_domains')));

        return $panel
            ->id('app')
            ->path($isCentral ? '{tenant}/app' : 'app')
            ->homeUrl(fn () => $isCentral
                ? "/" . (tenant('id') ?? request()->route('tenant') ?? request()->segment(1) ?? 'clinic1') . "/app"
                : "/app")
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/App/Resources'), for: 'App\Filament\App\Resources')
            ->discoverPages(in: app_path('Filament/App/Pages'), for: 'App\Filament\App\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/App/Widgets'), for: 'App\Filament\App\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                \Stancl\Tenancy\Middleware\InitializeTenancyByDomain::class,
                \Stancl\Tenancy\Middleware\InitializeTenancyByPath::class,
                \App\Http\Middleware\SetTenancyUrlDefaults::class,
                \App\Http\Middleware\SyncSpatiePermissionsTeamId::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                \App\Http\Middleware\ForceOnboardingMiddleware::class,
            ])
            ->plugins([
                // Shield removed - using custom role management
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}