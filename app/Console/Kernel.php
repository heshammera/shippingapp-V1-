<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * سجّل الأوامر المخصّصة
     */
    protected $commands = [
        \App\Console\Commands\InventoryBackfill::class, // 👈 مهم
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // مثال: شغّل الباكفيل يوميًا الساعة 2 صباحًا (بدون --dry)
        // هيكتب لوج في storage/logs/inventory_backfill.log
        $schedule->command('inventory:backfill')
            ->dailyAt('02:00')
            ->withoutOverlapping()
            ->onOneServer()
            ->appendOutputTo(storage_path('logs/inventory_backfill.log'));

        // ✅ فحص صلاحيات المستخدمين يومياً الساعة 9 صباحاً
        $schedule->command('users:check-expiration')
            ->dailyAt('09:00')
            ->appendOutputTo(storage_path('logs/user_expiration.log'));

        // 🚛 تحديث حالات الشحنات تلقائياً من شركات الشحن
        $schedule->job(new \App\Jobs\TrackShipmentsJob())->hourly();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        // ده بيحمّل أي أوامر جوه app/Console/Commands تلقائيًا كمان
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
