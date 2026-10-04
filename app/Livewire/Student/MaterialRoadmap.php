<?php

namespace App\Livewire\Student;

use App\Models\Topic;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class MaterialRoadmap extends Component
{
    public string $track = 'client';

    public function mount(string $track = 'client'): void
    {
        if (! in_array($track, ['client', 'server'], true)) {
            abort(404, 'Modul tidak ditemukan.');
        }

        $this->track = $track;
    }

    public function render()
    {
        $topics = Topic::query()
            ->with(['materials'])
            ->withCount(['questions' => fn ($q) => $q->where('status', 'published')])
            ->orderBy('position')
            ->get()
            ->filter(function ($topic) {
                $track = $topic->track?->value ?? $topic->track;

                return $track === $this->track;
            })
            ->values();

        $totalMaterials = $topics->sum(fn ($t) => $t->materials->count());
        $totalQuestions = $topics->sum('questions_count');

        $trackTitle = $this->track === 'server' ? 'Modul Server-Side' : 'Modul Client-Side';

        return view('livewire.student.material-roadmap', [
            'track' => $this->track,
            'trackTitle' => $trackTitle,
            'topics' => $topics,
            'totalMaterials' => $totalMaterials,
            'totalQuestions' => $totalQuestions,
        ])->title('Roadmap '.$trackTitle.' — Silabus LKS');
    }
}
