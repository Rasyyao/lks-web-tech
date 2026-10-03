<?php

namespace App\Policies;

use App\Models\PracticeAttempt;
use App\Models\User;

class AttemptPolicy
{
    /**
     * Only the owner can view their attempt.
     */
    public function view(User $user, PracticeAttempt $attempt): bool
    {
        return $user->id === $attempt->user_id;
    }

    /**
     * Only students can create attempts.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('student');
    }

    /**
     * Only the owner can answer in their attempt.
     */
    public function answer(User $user, PracticeAttempt $attempt): bool
    {
        return $user->id === $attempt->user_id && $attempt->isOpen();
    }
}
