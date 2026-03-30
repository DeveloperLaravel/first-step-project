<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
// 👇 أضف هذه
use Illuminate\Auth\Events\Login;
use App\Listeners\UpdateLastLogin;
class EventServiceProvider extends ServiceProvider
{
        protected $listen = [

        // 🔥 هنا المكان الصحيح
        Login::class => [
            UpdateLastLogin::class,
        ],
 \App\Events\RoleChanged::class => [
        \App\Listeners\SendRoleNotification::class,
    ],
    ];
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
