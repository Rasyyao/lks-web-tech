<?php

namespace App\Policies;

use App\Models\User;

class ProgressPolicy
{
    /**
     * User can view own progress, mentors and admins can view any student progress.
     */
    public function view(User $actor, User $target): bool
    {
        return $actor->id === $target->id || $actor->hasAnyRole(['mentor', 'admin']);
    }

    /**
     * Only the student can update their own progress.
     */
    public function update(User $actor, User $target): bool
    {
        return $actor->id === $target->id;
    }
}
