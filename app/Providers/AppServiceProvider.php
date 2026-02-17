<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
// اضف هذا السطر 👇
use Illuminate\Pagination\Paginator;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\DB;
        use App\Models\Shipment;
use App\Observers\ShipmentObserver;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Force HTTPS in production
        if($this->app->environment('production')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Force Bootstrap 5 pagination
        Paginator::useBootstrapFive();
        Shipment::observe(ShipmentObserver::class);
        \App\Models\StockMovement::observe(\App\Observers\StockMovementObserver::class);

        // Manually register Livewire components to fix Vercel discovery issues
        Livewire::component('app.filament.widgets.finance-stats-widget', \App\Filament\Widgets\FinanceStatsWidget::class);
        Livewire::component('app.filament.widgets.shipping-balances-widget', \App\Filament\Widgets\ShippingBalancesWidget::class);
        Livewire::component('app.filament.widgets.latest-collections-widget', \App\Filament\Widgets\LatestCollectionsWidget::class);
    }


}
