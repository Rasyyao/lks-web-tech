<?php

namespace App\Http\Controllers;

use App\Models\ModuleAsset;
use App\Models\Submission;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class FileDownloadController extends Controller
{
    /**
     * Download a module asset (via signed URL).
     */
    public function moduleAsset(ModuleAsset $asset)
    {
        if (! Storage::disk('private')->exists($asset->path)) {
            abort(404);
        }

        return Storage::disk('private')->download($asset->path, $asset->label);
    }

    /**
     * Download a submission ZIP (mentor/admin only, or the student owner).
     */
    public function submission(Submission $submission)
    {
        Gate::authorize('download', $submission);

        if (! Storage::disk('private')->exists($submission->path)) {
            abort(404);
        }

        return Storage::disk('private')->download($submission->path, $submission->original_name);
    }
}
