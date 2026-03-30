<?php

namespace App\Listeners;

use App\Events\RoleChanged;
use App\Models\User;
use App\Notifications\NewRoleNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendRoleNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(RoleChanged  $event): void
    {
       $users = User::role('admin')->get();

    foreach ($users as $user) {
        $user->notify(
            new NewRoleNotification(
                $event->action,
                $event->target
            )
        );
    }
    }
}
