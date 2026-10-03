<?php

namespace App\Policies;

use App\Models\Question;
use App\Models\User;

class QuestionPolicy
{
    /**
     * Mentors and admins can view questions.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['mentor', 'admin']);
    }

    /**
     * Mentors and admins can create questions.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['mentor', 'admin']);
    }

    /**
     * Mentors and admins can update questions.
     */
    public function update(User $user, Question $question): bool
    {
        return $user->hasRole(['mentor', 'admin']);
    }

    /**
     * Only a different mentor (not the author) can publish a question.
     */
    public function publish(User $user, Question $question): bool
    {
        if (! $user->hasRole(['mentor', 'admin'])) {
            return false;
        }

        // Author cannot publish their own question
        return $user->id !== $question->created_by;
    }

    /**
     * Only admins can delete questions.
     */
    public function delete(User $user, Question $question): bool
    {
        return $user->hasRole('admin');
    }
}
