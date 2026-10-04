<?php

namespace App\Livewire\Student;

use App\Models\Topic;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class MaterialShow extends Component
{
    public Topic $topic;

    public function mount(Topic $topic): void
    {
        $this->topic = $topic->load(['materials' => fn ($q) => $q->orderBy('position')]);
    }

    public function render()
    {
        return view('livewire.student.material-show', [
            'topic' => $this->topic,
        ])->title($this->topic->name.' — Materi');
    }
}
