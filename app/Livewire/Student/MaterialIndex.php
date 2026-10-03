<?php

namespace App\Livewire\Student;

use App\Models\Topic;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Silabus & Modul LKS Web Technologies')]
class MaterialIndex extends Component
{
    public function mount()
    {
        if (request()->has('jalur')) {
            $jalur = request()->query('jalur');
            if (in_array($jalur, ['client', 'server'], true)) {
                return redirect()->route('materials.roadmap', ['track' => $jalur]);
            }
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

        return view('livewire.student.material-index', [
            'clientStats' => $clientStats,
            'serverStats' => $serverStats,
        ]);
    }
}
