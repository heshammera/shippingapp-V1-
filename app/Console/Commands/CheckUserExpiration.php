<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
// use App\Notifications\UserExpirationNotification; // سنقوم بإنشائه لاحقاً

class CheckUserExpiration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:check-expiration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for users with expiring subscriptions and notify them';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 جاري التحقق من صلاحيات المستخدمين...');

        // 1. تحديد المستخدمين المنتهية صلاحيتهم لتعطيلهم
        $expiredUsers = User::where('is_active', true)
                            ->whereNotNull('expires_at')
                            ->where('expires_at', '<', now())
                            ->get();

        foreach ($expiredUsers as $user) {
            $user->update(['is_active' => false]);
            $this->warn("⛔ تم تعطيل حساب المستخدم: {$user->name} (انتهت الصلاحية)");
            \Illuminate\Support\Facades\Log::info("User deactivated due to expiration: {$user->id}");
            // يمكن إرسال إشعار هنا بأن الحساب توقف
        }

        // 2. تحديد المستخدمين الذين ستنتهي صلاحيتهم قريباً (مثلاً خلال 3 أيام)
        $expiringSoon = User::where('is_active', true)
                            ->whereNotNull('expires_at')
                            ->whereBetween('expires_at', [now(), now()->addDays(3)])
                            ->get();

        $count = 0;
        foreach ($expiringSoon as $user) {
            $daysLeft = now()->diffInDays($user->expires_at);
            $this->info("⚠️ المستخدم {$user->name} ستنتهي صلاحيته خلال {$daysLeft} أيّام.");
            
            try {
                // إرسال إشعار قاعدة البيانات (يظهر في Dashboard)
                $user->notify(new \App\Notifications\UserExpiringSoon($daysLeft));
                $this->info("   - تم إرسال إشعار Dashboard.");

                // إرسال بريد إلكتروني (إذا كان الإيميل صحيحاً)
                if (filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                    \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\UserExpirationNotice($user, $daysLeft));
                    $this->info("   - تم إرسال بريد إلكتروني.");
                }
            } catch (\Exception $e) {
                $this->error("   - فشل إرسال الإشعار: " . $e->getMessage());
                \Illuminate\Support\Facades\Log::error("Notification Error for User {$user->id}: " . $e->getMessage());
            }
            
            $count++;
        }

        $this->info("✅ تم الانتهاء. تم تعطيل " . $expiredUsers->count() . " حساب، وتنبيه " . $count . " مستخدم.");
    }
}
