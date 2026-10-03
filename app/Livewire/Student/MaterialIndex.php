<?php

namespace App\Livewire\Student;

use App\Models\Topic;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Silabus & Roadmap Materi LKS')]
class MaterialIndex extends Component
{
    #[Url(as: 'jalur')]
    public string $activeTrack = 'client';

    public function mount(): void
    {
        if (! in_array($this->activeTrack, ['client', 'server'], true)) {
            $this->activeTrack = 'client';
        }
    }

    public function setTrack(string $track): void
    {
        if (in_array($track, ['client', 'server'], true)) {
            $this->activeTrack = $track;
        }
    }

    public function render()
    {
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

        $activeTopics = $this->activeTrack === 'server' ? $serverTopics : $clientTopics;

        $clientStats = [
            'topics_count' => $clientTopics->count(),
            'materials_count' => $clientTopics->sum(fn ($t) => $t->materials->count()),
            'questions_count' => $clientTopics->sum('questions_count'),
        ];

        $serverStats = [
            'topics_count' => $serverTopics->count(),
            'materials_count' => $serverTopics->sum(fn ($t) => $t->materials->count()),
            'questions_count' => $serverTopics->sum('questions_count'),
        ];

        return view('livewire.student.material-index', [
            'clientTopics' => $clientTopics,
            'serverTopics' => $serverTopics,
            'activeTopics' => $activeTopics,
            'activeTrack' => $this->activeTrack,
            'clientStats' => $clientStats,
            'serverStats' => $serverStats,
        ]);
    }
}
