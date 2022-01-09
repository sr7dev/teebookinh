<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EventPolicy
{
    use HandlesAuthorization;

    public function is_owner(User $user, Event $event)
    {
        // die($event);
        return $user->is_admin == 1 || $user->id == $event->user_id;
    }
}
