<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\Import\QuestionCsvImporter;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ImportQuestions implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $csvPath,
        public int $creatorId
    ) {}

    public function handle(QuestionCsvImporter $importer): array
    {
        $creator = User::findOrFail($this->creatorId);

        return $importer->import($this->csvPath, $creator);
    }
}
