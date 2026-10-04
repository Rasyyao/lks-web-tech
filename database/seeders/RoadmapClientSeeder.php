<?php

namespace Database\Seeders;

use App\Models\DailyActivity;
use App\Models\Exercise;
use App\Models\RoadmapPage;
use App\Models\User;
use App\Services\Roadmap\ProgressTracker;
use App\Services\Roadmap\RoadmapContentImporter;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class RoadmapClientSeeder extends Seeder
{
    public function run(): void
    {
        $importer = app(RoadmapContentImporter::class);
        $importer->import(base_path('.claude/ROADMAP_CLIENT.md'));

        $tracker = app(ProgressTracker::class);

        $dewi = User::where('username', '541221001')->first();
        $raka = User::where('username', '541221002')->first();
        $dimas = User::where('username', '541221003')->first();

        // 1. Seed Dewi: Advanced Student
        if ($dewi) {
            $levels = RoadmapPage::where('kind', 'level')->whereIn('slug', ['level-0', 'level-1', 'level-2', 'level-3'])->get();
            foreach ($levels as $lvl) {
                foreach ($lvl->sections as $sec) {
                    $tracker->markSectionRead($dewi, $sec->slug);
                }
                foreach ($lvl->checkpoints as $cp) {
                    $tracker->toggleCheckpoint($dewi, $cp->slug, true);
                }
            }

            // Pass Level 3 exercises
            $l3Exercises = Exercise::where('level_slug', 'level-3')->get();
            foreach ($l3Exercises as $ex) {
                $tracker->recordExerciseAttempt(
                    $dewi,
                    $ex,
                    $ex->starter_code ?? '// solved by dewi',
                    true,
                    [['passed' => true]],
                    120
                );
            }

            // Daily activity over past 3 days
            DailyActivity::updateOrCreate(
                ['user_id' => $dewi->id, 'date' => Carbon::today()->subDays(2)->toDateString()],
                ['active_seconds' => 3600, 'last_beat_at' => now()->subDays(2)]
            );
            DailyActivity::updateOrCreate(
                ['user_id' => $dewi->id, 'date' => Carbon::today()->subDay()->toDateString()],
                ['active_seconds' => 4500, 'last_beat_at' => now()->subDay()]
            );
            DailyActivity::updateOrCreate(
                ['user_id' => $dewi->id, 'date' => Carbon::today()->toDateString()],
                ['active_seconds' => 2700, 'last_beat_at' => now()]
            );
        }

        // 2. Seed Raka: Moderate Student
        if ($raka) {
            $levels = RoadmapPage::where('kind', 'level')->whereIn('slug', ['level-0', 'level-1'])->get();
            foreach ($levels as $lvl) {
                foreach ($lvl->sections as $sec) {
                    $tracker->markSectionRead($raka, $sec->slug);
                }
                foreach ($lvl->checkpoints as $cp) {
                    $tracker->toggleCheckpoint($raka, $cp->slug, true);
                }
            }

            DailyActivity::updateOrCreate(
                ['user_id' => $raka->id, 'date' => Carbon::today()->subDay()->toDateString()],
                ['active_seconds' => 2400, 'last_beat_at' => now()->subDay()]
            );
            DailyActivity::updateOrCreate(
                ['user_id' => $raka->id, 'date' => Carbon::today()->toDateString()],
                ['active_seconds' => 1800, 'last_beat_at' => now()]
            );
        }

        // 3. Seed Dimas: Novice Student
        if ($dimas) {
            $level0 = RoadmapPage::where('slug', 'level-0')->first();
            if ($level0) {
                if ($level0->sections->isNotEmpty()) {
                    $tracker->markSectionRead($dimas, $level0->sections->first()->slug);
                }
                if ($level0->checkpoints->isNotEmpty()) {
                    $tracker->toggleCheckpoint($dimas, $level0->checkpoints->first()->slug, true);
                }
            }

            DailyActivity::updateOrCreate(
                ['user_id' => $dimas->id, 'date' => Carbon::today()->toDateString()],
                ['active_seconds' => 1200, 'last_beat_at' => now()]
            );
        }
    }
}
