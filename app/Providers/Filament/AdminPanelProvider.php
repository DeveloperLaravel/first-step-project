<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Http\Middleware\CheckUserActive;
use App\Http\Middleware\UpdateLastSeen;
use App\Http\Middleware\UpdateUserActivity;
use Filament\Navigation\UserMenuItem;

class AdminPanelProvider extends PanelProvider
{

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('نظام إدارة المستخدمين')
            ->registration()
->databaseNotifications()
->databaseNotificationsPolling('15s')
->userMenuItems([
            UserMenuItem::make()
                ->label('الملف الشخصي')
                ->url('/profile')
                ->icon('heroicon-o-user'),

            UserMenuItem::make()
                ->label('تسجيل الخروج')
                ->url('/logout')
        ])
  ->widgets([
            \App\Filament\Widgets\StatsOverview::class,
            \App\Filament\Widgets\UsersChart::class,
            \App\Filament\Widgets\LatestUsers::class,
            \App\Filament\Widgets\OnlineUsers::class,
        ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                UpdateLastSeen::class,

            ])
            ->authMiddleware([
                Authenticate::class,
                CheckUserActive::class,
            ]);
    }
}
