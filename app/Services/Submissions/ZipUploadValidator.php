<?php

namespace App\Services\Submissions;

use App\Models\Module;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Http\UploadedFile;

class ZipUploadValidator
{
    /**
     * Maximum file size in kilobytes (20 MB).
     */
    public const MAX_SIZE_KB = 20480;

    /**
     * Validate the uploaded ZIP file.
     *
     * @return array{valid: bool, error: ?string}
     */
    public function validate(UploadedFile $file, Module $module, User $user): array
    {
        // 1. Check if module is open
        if (! $module->isOpen()) {
            return [
                'valid' => false,
                'error' => __('modules.closed'),
            ];
        }

        // 2. Check daily submission limit
        $todaySubmissionsCount = Submission::where('user_id', $user->id)
            ->where('module_id', $module->id)
            ->whereDate('created_at', today())
            ->count();

        if ($todaySubmissionsCount >= $module->max_attempts_per_day) {
            return [
                'valid' => false,
                'error' => __('modules.daily_limit_reached'),
            ];
        }

        // 3. Extension check
        $ext = strtolower($file->getClientOriginalExtension());
        if ($ext !== 'zip') {
            return [
                'valid' => false,
                'error' => __('modules.invalid_zip'),
            ];
        }

        // 4. Size check (max 20 MB)
        if ($file->getSize() > self::MAX_SIZE_KB * 1024) {
            return [
                'valid' => false,
                'error' => __('modules.file_too_large'),
            ];
        }

        // 5. MIME type check
        $mime = $file->getMimeType();
        $allowedMimes = [
            'application/zip',
            'application/x-zip-compressed',
            'multipart/x-zip',
            'application/x-compressed',
            'application/octet-stream',
        ];

        if (! in_array($mime, $allowedMimes, true)) {
            return [
                'valid' => false,
                'error' => __('modules.invalid_zip'),
            ];
        }

        // 6. Magic bytes check (ZIP begins with "PK\x03\x04" or empty ZIP "PK\x05\x06")
        $handle = fopen($file->getRealPath(), 'rb');
        if (! $handle) {
            return [
                'valid' => false,
                'error' => __('modules.invalid_zip'),
            ];
        }

        $header = fread($handle, 4);
        fclose($handle);

        if (! str_starts_with($header, "PK\x03\x04") && ! str_starts_with($header, "PK\x05\x06")) {
            return [
                'valid' => false,
                'error' => __('modules.invalid_zip'),
            ];
        }

        return [
            'valid' => true,
            'error' => null,
        ];
    }
}
