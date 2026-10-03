<?php

namespace App\Services\Submissions;

use App\Enums\SubmissionStatus;
use App\Models\Module;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SubmissionStorage
{
    /**
     * Store the uploaded ZIP on the private disk and create a Submission record.
     */
    public function store(UploadedFile $file, Module $module, User $user): Submission
    {
        $hash = Str::random(40);
        $filename = "{$hash}.zip";
        $directory = "submissions/{$module->id}/{$user->id}";

        // Store file onto private disk
        $path = $file->storeAs($directory, $filename, 'private');

        $sha256 = hash_file('sha256', $file->getRealPath());
        $size = $file->getSize();
        $originalName = $file->getClientOriginalName();

        // Compute next attempt number for this student and module
        $lastAttempt = Submission::where('user_id', $user->id)
            ->where('module_id', $module->id)
            ->max('attempt_no') ?? 0;

        return Submission::create([
            'user_id' => $user->id,
            'module_id' => $module->id,
            'attempt_no' => $lastAttempt + 1,
            'path' => $path,
            'original_name' => $originalName,
            'size' => $size,
            'sha256' => $sha256,
            'status' => SubmissionStatus::Received,
        ]);
    }
}
