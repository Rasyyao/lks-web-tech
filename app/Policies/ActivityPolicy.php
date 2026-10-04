<?php

namespace App\Policies;

use App\Models\User;

class ActivityPolicy
{
    /**
     * User can view own activity, mentors and admins can view any student activity.
     */
    public function view(User $actor, User $target): bool
    {
        return $actor->id === $target->id || $actor->hasAnyRole(['mentor', 'admin']);
    }

    /**
     * Any active authenticated user can send heartbeat and record learning events.
     */
    public function pulse(User $user): bool
    {
        return $user->is_active;
    }
}
