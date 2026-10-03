<?php

namespace App\Livewire\Student;

use App\Models\Topic;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Materi & Roadmap')]
class MaterialIndex extends Component
{
    public function render()
    {
        $topics = Topic::with(['materials'])
            ->withCount(['questions' => fn ($q) => $q->where('status', 'published')])
            ->orderBy('position')
            ->get();

        return view('livewire.student.material-index', [
            'topics' => $topics,
        ]);
    }
}
