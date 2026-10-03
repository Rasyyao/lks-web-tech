<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Only admins can view user list.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Only admins can create users.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Only admins can update other users.
     */
    public function update(User $user, User $model): bool
    {
        // Users can update their own profile
        if ($user->id === $model->id) {
            return true;
        }

        return $user->hasRole('admin');
    }

    /**
     * Only admins can deactivate users.
     */
    public function deactivate(User $user, User $model): bool
    {
        // Cannot deactivate yourself
        if ($user->id === $model->id) {
            return false;
        }

        return $user->hasRole('admin');
    }

    /**
     * Only admins can reset passwords.
     */
    public function resetPassword(User $user, User $model): bool
    {
        return $user->hasRole('admin');
    }
}
