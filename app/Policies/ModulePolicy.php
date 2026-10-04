<?php

namespace App\Policies;

use App\Models\Module;
use App\Models\User;

class ModulePolicy
{
    /**
     * Students can view published modules assigned to their cohort.
     */
    public function view(User $user, Module $module): bool
    {
        if ($user->hasRole(['mentor', 'admin'])) {
            return true;
        }

        return $module->isVisibleTo($user);
    }

    /**
     * Only mentors and admins can create modules.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['mentor', 'admin']);
    }

    /**
     * Only mentors and admins can update modules.
     */
    public function update(User $user, Module $module): bool
    {
        return $user->hasRole(['mentor', 'admin']);
    }

    /**
     * Only admins can delete modules.
     */
    public function delete(User $user, Module $module): bool
    {
        return $user->hasRole('admin');
    }
}
