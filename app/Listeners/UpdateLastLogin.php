<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Auth\Events\Login;
class UpdateLastLogin
{
    /**
     * Create the event listener.
     */

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $user = $event->user;

        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => request()->ip(),
        ]);
    }
}
