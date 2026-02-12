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

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Teal,
                'gray' => Color::Slate,
            ])
            ->font('Tajawal')
            ->brandName('Orderly')
            ->brandLogo(asset('logo.png')) // Use the logo we copied
            ->brandLogoHeight('3rem')
            ->favicon(asset('logo.png'))
            ->darkMode(true)
            ->topNavigation()
            ->maxContentWidth('full')
            ->sidebarCollapsibleOnDesktop()
            ->collapsibleNavigationGroups(true)
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
                \App\Filament\Pages\AccountingDashboard::class,
            ])
            // ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                \App\Filament\Widgets\StatsOverview::class,
                \App\Filament\Widgets\ShipmentsCharWidget::class,
                \App\Filament\Widgets\FinancialChart::class,
                \App\Filament\Widgets\DelayedShipmentsWidget::class,
                \App\Filament\Widgets\LowStockAlertWidget::class,
                \App\Filament\Widgets\ShippingCompanyPerformanceWidget::class,
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
            ])
            ->renderHook(
                'panels::head.end',
                fn () => \Illuminate\Support\Facades\Blade::render(<<<HTML
                <link rel="manifest" href="/manifest.json">
                <meta name="apple-mobile-web-app-capable" content="yes">
                <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
                <meta name="theme-color" content="#0284c7">
                
                <script>
                    if ('serviceWorker' in navigator) {
                        window.addEventListener('load', () => {
                            navigator.serviceWorker.register('/sw.js');
                        });
                    }
                </script>

                <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@200;300;400;500;700;800;900&display=swap" rel="stylesheet">
                <style>
                    /* Custom Font */
                    body, .fi-body { font-family: "Tajawal", sans-serif !important; }
                    
                    /* Mobile Bottom Navigation */
                    .mobile-nav {
                        display: none;
                        position: fixed;
                        bottom: 0;
                        left: 0;
                        right: 0;
                        height: 65px;
                        background: rgba(255, 255, 255, 0.9);
                        backdrop-filter: blur(10px);
                        border-top: 1px solid rgba(229, 231, 235, 0.5);
                        z-index: 50;
                        justify-content: space-around;
                        align-items: center;
                        padding-bottom: env(safe-area-inset-bottom);
                    }
                    .dark .mobile-nav {
                        background: rgba(31, 41, 55, 0.9);
                        border-color: rgba(75, 85, 99, 0.5);
                    }

                    @media (max-width: 768px) {
                        .mobile-nav { display: flex; }
                        .fi-sidebar { padding-bottom: 80px; }
                        .fi-main { padding-bottom: 80px; }
                        /* Hide desktop sidebar on mobile to save space */
                        .fi-sidebar-close-button { display: none !important; }
                    }

                    .mobile-nav-item {
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        color: #6b7280; /* text-gray-500 */
                        font-size: 0.7rem;
                        text-decoration: none;
                    }
                    .mobile-nav-item.active {
                        color: #0284c7; /* primary-600 */
                    }
                    .mobile-nav-item svg { width: 24px; height: 24px; margin-bottom: 2px; }

                    /* Floating Card Style */
                    .fi-section, .fi-wi-stats-overview-card, .fi-ta-ctn {
                        border-radius: 0.75rem !important;
                        border: 1px solid #f3f4f6 !important;
                        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
                    }
                    .dark .fi-section, .dark .fi-wi-stats-overview-card, .dark .fi-ta-ctn {
                        border-color: #374151 !important;
                    }

                    /* ... (Keep other existing styles) ... */
                    .tr-success { background-color: rgba(45, 212, 191, 0.15) !important; }
                    .tr-danger { background-color: rgba(248, 113, 113, 0.15) !important; }
                    .tr-warning { background-color: rgba(251, 146, 60, 0.15) !important; }
                    .tr-info { background-color: rgba(56, 189, 248, 0.15) !important; }
                    .tr-primary { background-color: rgba(129, 140, 248, 0.15) !important; }
                    .tr-gray { background-color: rgba(156, 163, 175, 0.15) !important; }
                    
                    .tr-purple { background-color: rgba(168, 85, 247, 0.15) !important; }
                    .tr-pink { background-color: rgba(236, 72, 153, 0.15) !important; }
                    .tr-rose { background-color: rgba(244, 63, 94, 0.15) !important; }
                    .tr-amber { background-color: rgba(245, 158, 11, 0.15) !important; }
                    .tr-lime { background-color: rgba(132, 204, 22, 0.15) !important; }
                    .tr-emerald { background-color: rgba(16, 185, 129, 0.15) !important; }
                    .tr-teal { background-color: rgba(20, 184, 166, 0.15) !important; }
                    .tr-cyan { background-color: rgba(6, 182, 212, 0.15) !important; }
                    .tr-sky { background-color: rgba(14, 165, 233, 0.15) !important; }
                    .tr-violet { background-color: rgba(139, 92, 246, 0.15) !important; }
                    .tr-fuchsia { background-color: rgba(217, 70, 239, 0.15) !important; }
                    .tr-slate { background-color: rgba(100, 116, 139, 0.25) !important; }
                    
                    .fi-ta-cell { border-bottom: 2px dashed #e5e7eb !important; }
                    .dark .fi-ta-cell { border-bottom: 2px dashed #374151 !important; }
                    tr:last-child .fi-ta-cell { border-bottom: none !important; }

                    /* Optimize Tables for Mobile */
                    @media (max-width: 640px) {
                        .fi-ta-content { height: auto !important; max-height: none !important; }
                        .fi-ta-header { display: none !important; } /* Hide headers on mobile list */
                        .fi-ta-row { 
                            display: flex !important; 
                            flex-direction: column !important; 
                            margin-bottom: 1rem !important;
                            border: 1px solid #eee !important;
                            border-radius: 12px !important;
                            padding: 10px !important;
                        }
                        .dark .fi-ta-row { border-color: #374151 !important; }
                        .fi-ta-cell { border-bottom: none !important; width: 100% !important; padding: 5px 0 !important; }
                    }
                    
                    .fi-ta-content::-webkit-scrollbar { height: 14px !important; width: 14px !important; }
                    .fi-ta-content::-webkit-scrollbar-thumb { background-color: rgba(156, 163, 175, 0.5); border-radius: 6px; border: 3px solid transparent; background-clip: content-box; }
                    .dark .invert-on-dark { filter: invert(1) !important; mix-blend-mode: normal !important; opacity: 1 !important; }
                </style>
HTML
                )
            )
            ->renderHook(
                'panels::body.end',
                fn () => \Illuminate\Support\Facades\Blade::render(<<<HTML
                <nav class="mobile-nav">
                    <a href="/admin" class="mobile-nav-item {{ request()->is('admin') ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                        </svg>
                        <span>الرئيسية</span>
                    </a>
                    <a href="/admin/shipments" class="mobile-nav-item {{ request()->is('admin/shipments*') ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177V3.945c0-.621-.504-1.125-1.125-1.125H10.5M4.875 14.25v-1.125a1.125 1.125 0 0 1 1.125-1.125H10.5M4.875 14.25h12m0 0v-1.125a1.125 1.125 0 0 0-1.125-1.125H10.5" />
                        </svg>
                        <span>الشحنات</span>
                    </a>
                    <a href="/admin/barcode-scanner" class="mobile-nav-item {{ request()->is('admin/barcode-scanner*') ? 'active' : '' }}" style="background: #0284c7; color: white; border-radius: 50%; width: 50px; height: 50px; margin-top: -30px; border: 4px solid white; box-shadow: 0 4px 10px rgba(0,0,0,0.2);">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z" />
                          <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75ZM6.75 16.5h.75v.75h-.75v-.75ZM16.5 6.75h.75v.75h-.75v-.75ZM13.5 13.5h.75v.75h-.75v-.75ZM13.5 19.5h.75v.75h-.75v-.75ZM19.5 13.5h.75v.75h-.75v-.75ZM19.5 19.5h.75v.75h-.75v-.75ZM16.5 16.5h.75v.75h-.75v-.75Z" />
                        </svg>
                    </a>
                    <a href="/admin/products" class="mobile-nav-item {{ request()->is('admin/products*') ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                        </svg>
                        <span>المخزون</span>
                    </a>
                    <a href="/admin/ai-assistant" class="mobile-nav-item {{ request()->is('admin/ai-assistant*') ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 22.25l-.394-1.683a2.25 2.25 0 0 0-1.623-1.623L12.75 18.5l1.683-.394a2.25 2.25 0 0 0 1.623-1.623l.394-1.683.394 1.683a2.25 2.25 0 0 0 1.623 1.623l1.683.394-1.683.394a2.25 2.25 0 0 0-1.623 1.623Z" />
                        </svg>
                        <span>الذكاء</span>
                    </a>
                </nav>
                @livewire('floating-ai-chat')
HTML
                )
            )
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
