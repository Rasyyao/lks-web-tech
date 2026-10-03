<?php

namespace App\Livewire\Student;

use App\Enums\ModuleStatus;
use App\Enums\ModuleTrack;
use App\Models\Module;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Daftar Modul')]
class ModuleIndex extends Component
{
    #[Url]
    public string $track = 'all';

    #[Url]
    public string $level = 'all';

    public function render()
    {
        $user = Auth::user();
        $cohortIds = $user->cohorts->pluck('id');

        $query = Module::query()
            ->where('status', ModuleStatus::Published)
            ->whereHas('cohorts', fn ($q) => $q->whereIn('cohorts.id', $cohortIds))
            ->with(['submissions' => fn ($q) => $q->where('user_id', $user->id)])
            ->orderBy('level')
            ->orderBy('title');

        if ($this->track !== 'all' && ModuleTrack::tryFrom($this->track)) {
            $query->where('track', $this->track);
        }

        if ($this->level !== 'all' && is_numeric($this->level)) {
            $query->where('level', (int) $this->level);
        }

        $modules = $query->get();

        return view('livewire.student.module-index', [
            'modules' => $modules,
            'tracks' => ModuleTrack::cases(),
        ]);
    }
}
