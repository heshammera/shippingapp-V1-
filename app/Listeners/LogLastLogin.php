<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Auth\Events\Login;

class LogLastLogin
{
    public function handle(Login $event): void
    {
        $event->user->update(['last_login_at' => now()]);
    }
}

