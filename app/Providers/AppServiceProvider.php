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

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // اجبار Laravel يستخدم bootstrap-5
        Paginator::useBootstrapFive();
        Shipment::observe(ShipmentObserver::class);
        \App\Models\StockMovement::observe(\App\Observers\StockMovementObserver::class);
    }


}
