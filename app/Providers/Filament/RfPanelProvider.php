<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Http\Middleware\ResolveTenant;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class RfPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('rf')
            ->path('rf')
            ->login()
            ->brandName('RF Scanner')
            ->colors([
                'primary' => Color::Emerald,
            ])
            ->discoverResources(in: app_path('Filament/Rf/Resources'), for: 'App\\Filament\\Rf\\Resources')
            ->discoverPages(in: app_path('Filament/Rf/Pages'), for: 'App\\Filament\\Rf\\Pages')
            ->discoverWidgets(in: app_path('Filament/Rf/Widgets'), for: 'App\\Filament\\Rf\\Widgets')
            ->pages([
                Dashboard::class,
                \App\Filament\Rf\Pages\RfDashboard::class,
                \App\Filament\Rf\Pages\ReceiveScanPage::class,
                \App\Filament\Rf\Pages\PutawayScanPage::class,
                \App\Filament\Rf\Pages\PickScanPage::class,
                \App\Filament\Rf\Pages\PackScanPage::class,
                \App\Filament\Rf\Pages\CountScanPage::class,
            ])
            ->widgets([
                AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                ResolveTenant::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
