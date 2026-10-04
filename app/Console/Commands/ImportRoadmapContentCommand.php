<?php

namespace App\Console\Commands;

use App\Services\Roadmap\RoadmapContentImporter;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('content:import {--file= : Path to ROADMAP_CLIENT.md markdown file}')]
#[Description('Import or sync learning roadmap content and exercises from markdown into database')]
class ImportRoadmapContentCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(RoadmapContentImporter $importer): int
    {
        $filePath = $this->option('file') ?: base_path('.claude/ROADMAP_CLIENT.md');

        if (! file_exists($filePath)) {
            $this->error("Roadmap file not found at: {$filePath}");

            return self::FAILURE;
        }

        $this->info("Importing roadmap content from: {$filePath}...");

        try {
            $stats = $importer->import($filePath);

            $this->table(
                ['Entity', 'Count'],
                [
                    ['Pages (Levels & References)', $stats['pages']],
                    ['Sections (Materi, Latihan, etc.)', $stats['sections']],
                    ['Checkpoints (Cek Pemahaman)', $stats['checkpoints']],
                    ['Interactive Exercises', $stats['exercises']],
                ]
            );

            $this->info('Roadmap content imported successfully!');

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("Import failed: {$e->getMessage()}");

            return self::FAILURE;
        }
    }
}
