<?php

namespace App\Policies;

use App\Models\ExerciseAttempt;
use App\Models\User;

class ExerciseAttemptPolicy
{
    /**
     * Only the student or a mentor/admin can view an attempt.
     */
    public function view(User $user, ExerciseAttempt $attempt): bool
    {
        return $user->id === $attempt->user_id || $user->hasAnyRole(['mentor', 'admin']);
    }

    /**
     * Only students can create exercise attempts.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('student');
    }
}
