<?php

namespace App\Jobs;

use App\Services\Import\StudentCsvImporter;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ImportStudents implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $csvPath,
        public ?int $defaultCohortId = null
    ) {}

    public function handle(StudentCsvImporter $importer): array
    {
        return $importer->import($this->csvPath, $this->defaultCohortId);
    }
}
