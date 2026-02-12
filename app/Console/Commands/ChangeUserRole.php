<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;  // تأكد من استيراد موديل User

class ChangeUserRole extends Command
{
    /**
     * اسم الكوماند.
     *
     * @var string
     */
    protected $signature = 'user:change-role {user_id} {role}'; // يطلب الـ user_id والدور الجديد

    /**
     * الوصف المختصر للكوماند.
     *
     * @var string
     */
    protected $description = 'تغيير دور المستخدم بناءً على المعرف (user_id)';

// تنفيذ الكوماند
    public function handle()
    {
        // جلب المستخدم باستخدام الـ user_id
        $user = User::find($this->argument('user_id'));

        // تحقق إذا كان المستخدم موجودًا
        if (!$user) {
            $this->error('المستخدم غير موجود!');
            return;
        }

        // تحقق إذا كان الدور صحيح
        $role = $this->argument('role');
        if (!in_array($role, ['admin', 'moderator', 'user'])) {
            $this->error('الدور غير صالح! يجب أن يكون admin أو moderator أو user');
            return;
        }

        // تغيير الدور وحفظه
        $user->role = $role;
        $user->save();

        $this->info('تم تغيير دور المستخدم بنجاح!');
    }
}
