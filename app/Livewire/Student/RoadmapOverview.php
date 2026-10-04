<?php

namespace App\Livewire\Student;

use App\Models\DailyActivity;
use App\Models\LevelProgress;
use App\Models\RoadmapPage;
use App\Models\Topic;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.app')]
class RoadmapOverview extends Component
{
    #[Url]
    public string $tab = 'roadmap';

    public function setTab(string $tab): void
    {
        $this->tab = in_array($tab, ['roadmap', 'silabus', 'client', 'server'], true) ? $tab : 'roadmap';
    }

    public function render(): View
    {
        $user = Auth::user();

        $levels = RoadmapPage::where('kind', 'level')
            ->orderBy('position')
            ->with(['checkpoints', 'exercises', 'sections'])
            ->get();

        $references = RoadmapPage::whereIn('kind', ['reference', 'guide'])
            ->orderBy('position')
            ->get();

        $userProgress = LevelProgress::where('user_id', $user->id)
            ->get()
            ->keyBy('level_slug');

        $totalLevels = $levels->count();
        $completedLevels = $userProgress->where('is_completed', true)->count();
        $overallPercent = $totalLevels > 0 ? (int) round(($completedLevels / $totalLevels) * 100) : 0;

        $totalActiveSeconds = (int) DailyActivity::where('user_id', $user->id)->sum('active_seconds');
        $activeHours = round($totalActiveSeconds / 3600, 1);

        // Find next unfinished and unlocked level to resume
        $nextLevel = null;
        foreach ($levels as $lvl) {
            $prog = $userProgress->get($lvl->slug);
            if (! $prog || ! $prog->is_completed) {
                if ($lvl->isUnlockedFor($user)) {
                    $nextLevel = $lvl;
                }
                break;
            }
        }

        // Topics for Silabus & Modul Topik
        $allTopics = Topic::query()
            ->with(['materials'])
            ->withCount(['questions' => fn ($q) => $q->where('status', 'published')])
            ->orderBy('position')
            ->get();

        $clientTopics = $allTopics->filter(function ($topic) {
            $track = $topic->track?->value ?? $topic->track;

            return $track === 'client';
        })->values();

        $serverTopics = $allTopics->filter(function ($topic) {
            $track = $topic->track?->value ?? $topic->track;

            return $track === 'server';
        })->values();

        $clientStats = [
            'topics_count' => $clientTopics->count(),
            'materials_count' => $clientTopics->sum(fn ($t) => $t->materials->count()),
            'questions_count' => $clientTopics->sum('questions_count'),
            'topics_preview' => $clientTopics->pluck('name')->all(),
        ];

        $serverStats = [
            'topics_count' => $serverTopics->count(),
            'materials_count' => $serverTopics->sum(fn ($t) => $t->materials->count()),
            'questions_count' => $serverTopics->sum('questions_count'),
            'topics_preview' => $serverTopics->pluck('name')->all(),
        ];

        // Map official syllabus stages directly to the concrete roadmap levels
        $stageDefinitions = [
            [
                'stage' => 1,
                'slug' => 'html-semantics',
                'name' => 'HTML5 Foundation & Web Semantics',
                'summary' => 'Fondasi struktur web standar W3C, tag semantik, aksesibilitas, formulir & validasi HTML5, dan dasar visual vektor SVG.',
                'level_slugs' => ['level-0', 'level-1'],
            ],
            [
                'stage' => 2,
                'slug' => 'css-layout',
                'name' => 'Modern CSS, Flexbox & Grid Layout',
                'summary' => 'Tata letak responsif modern tanpa framework berat: Flexbox 1D, CSS Grid 2D responsif, styling visual peta, dan CSS transition.',
                'level_slugs' => ['level-2'],
            ],
            [
                'stage' => 3,
                'slug' => 'javascript-core',
                'name' => 'Modern JavaScript (ES6+) & Core Logic',
                'summary' => 'Logika pemrograman modern ES6+: arrow functions, array functional (map/filter/reduce), state model, render loop, dan LocalStorage.',
                'level_slugs' => ['level-3', 'level-4'],
            ],
            [
                'stage' => 4,
                'slug' => 'javascript-dom',
                'name' => 'JavaScript DOM Manipulation & Events',
                'summary' => 'Interaksi UI tingkat lanjut: kalkulasi koordinat, rendering dinamis, zoom/pan transform SVG, algoritma traversal (BFS/DFS), dan optimasi.',
                'level_slugs' => ['level-5', 'level-6', 'level-7', 'level-8'],
            ],
        ];

        $clientTopicsBySlug = $clientTopics->keyBy('slug');

        $stages = collect($stageDefinitions)->map(function ($st) use ($levels, $clientTopicsBySlug) {
            $st['topic'] = $clientTopicsBySlug->get($st['slug']);
            $st['levels'] = $levels->whereIn('slug', $st['level_slugs'])->values();

            return $st;
        });

        return view('livewire.student.roadmap-overview', [
            'tab' => $this->tab,
            'stages' => $stages,
            'levels' => $levels,
            'references' => $references,
            'userProgress' => $userProgress,
            'completedLevels' => $completedLevels,
            'totalLevels' => $totalLevels,
            'overallPercent' => $overallPercent,
            'activeHours' => $activeHours,
            'nextLevel' => $nextLevel ?? $levels->first(),
            'clientTopics' => $clientTopics,
            'serverTopics' => $serverTopics,
            'clientStats' => $clientStats,
            'serverStats' => $serverStats,
        ])->title('Belajar: Roadmap & Silabus Terpadu LKS Web Technologies');
    }
}
