<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;  // تأكد من استدعاء موديل User

class ShowUsers extends Command
{
    /**
     * اسم الكوماند (اسم مميز لتشغيل الكوماند).
     *
     * @var string
     */
    protected $signature = 'show:users'; // الكوماند اللي هتنفذه

    /**
     * الوصف المختصر للكوماند.
     *
     * @var string
     */
    protected $description = 'عرض عدد المستخدمين في قاعدة البيانات مع بياناتهم'; // وصف الكوماند

    /**
     * تنفيذ الكوماند.
     *
     * @return void
     */
    public function handle()
    {
        // جلب كل المستخدمين
        $users = User::all();

        // حساب عدد المستخدمين
        $userCount = $users->count();

        // عرض البيانات على التيرمنال
        $this->info("عدد المستخدمين: $userCount");
        
        // عرض بيانات كل مستخدم
        foreach ($users as $user) {
            $this->line("اسم المستخدم: {$user->name} - البريد الإلكتروني: {$user->email} - الدور: {$user->role}");
        }
    }
}
