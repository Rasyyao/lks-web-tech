<?php

namespace App\Livewire\Student;

use App\Models\RoadmapPage;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class RoadmapReference extends Component
{
    public string $slug;

    public RoadmapPage $page;

    public function mount(string $slug): void
    {
        $this->slug = $slug;
        $this->page = RoadmapPage::where('slug', $slug)
            ->whereIn('kind', ['reference', 'guide'])
            ->with(['sections'])
            ->firstOrFail();
    }

    public function render(): View
    {
        $allReferences = RoadmapPage::whereIn('kind', ['reference', 'guide'])
            ->orderBy('position')
            ->get();

        return view('livewire.student.roadmap-reference', [
            'page' => $this->page,
            'allReferences' => $allReferences,
        ])->title($this->page->title.' — Panduan LKS');
    }
}
