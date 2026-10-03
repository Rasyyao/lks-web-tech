<?php

namespace Tests\Feature;

use App\Models\Module;
use App\Models\User;
use App\Services\Submissions\SubmissionStorage;
use App\Services\Submissions\ZipUploadValidator;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SubmissionUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        Storage::fake('private');
    }

    public function test_validator_refuses_non_zip_file(): void
    {
        $dewi = User::where('username', '541221001')->first();
        $module = Module::first();
        $validator = new ZipUploadValidator();

        // Create a fake text file renamed as .zip
        $fakeFile = UploadedFile::fake()->create('project.zip', 100, 'text/plain');

        $result = $validator->validate($fakeFile, $module, $dewi);

        $this->assertFalse($result['valid']);
    }

    public function test_validator_refuses_oversized_file(): void
    {
        $dewi = User::where('username', '541221001')->first();
        $module = Module::first();
        $validator = new ZipUploadValidator();

        // 25 MB file (over 20 MB limit)
        $oversizedFile = UploadedFile::fake()->create('heavy.zip', 26000, 'application/zip');

        $result = $validator->validate($oversizedFile, $module, $dewi);

        $this->assertFalse($result['valid']);
        $this->assertEquals(__('modules.file_too_large'), $result['error']);
    }

    public function test_valid_zip_is_stored_on_private_disk(): void
    {
        $dewi = User::where('username', '541221001')->first();
        $module = Module::first();
        $storage = new SubmissionStorage();

        // Create genuine ZIP file in memory (starts with PK\x03\x04)
        $zipContent = "PK\x03\x04" . str_repeat("\x00", 50);
        $tempPath = tempnam(sys_get_temp_dir(), 'zip_test');
        file_put_contents($tempPath, $zipContent);

        $file = new UploadedFile($tempPath, 'project.zip', 'application/zip', null, true);

        $submission = $storage->store($file, $module, $dewi);

        $this->assertNotNull($submission->id);
        $this->assertEquals('project.zip', $submission->original_name);
        Storage::disk('private')->assertExists($submission->path);

        @unlink($tempPath);
    }
}
