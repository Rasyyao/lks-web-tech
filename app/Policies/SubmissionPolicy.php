<?php

namespace App\Policies;

use App\Models\Submission;
use App\Models\User;

class SubmissionPolicy
{
    /**
     * Owner, mentors, and admins can view a submission.
     */
    public function view(User $user, Submission $submission): bool
    {
        if ($user->id === $submission->user_id) {
            return true;
        }

        return $user->hasRole(['mentor', 'admin']);
    }

    /**
     * Only students can create submissions.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('student');
    }

    /**
     * Owner, mentors, and admins can download a submission.
     */
    public function download(User $user, Submission $submission): bool
    {
        if ($user->id === $submission->user_id) {
            return true;
        }

        return $user->hasRole(['mentor', 'admin']);
    }

    /**
     * Only mentors and admins can review (score) a submission.
     */
    public function review(User $user, Submission $submission): bool
    {
        return $user->hasRole(['mentor', 'admin']);
    }
}
