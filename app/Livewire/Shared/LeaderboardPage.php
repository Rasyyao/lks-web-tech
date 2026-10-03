<?php

namespace App\Livewire\Shared;

use App\Models\Cohort;
use App\Models\LeaderboardEntry;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Papan Peringkat')]
class LeaderboardPage extends Component
{
    #[Url]
    public string $cohort = 'all';

    public function render()
    {
        $query = LeaderboardEntry::query()
            ->with(['user.cohorts'])
            ->whereHas('user', fn ($q) => $q->where('is_active', true))
            ->orderByDesc('total')
            ->orderBy('reached_at');

        if ($this->cohort !== 'all' && is_numeric($this->cohort)) {
            $cohortId = (int) $this->cohort;
            $query->whereHas('user.cohorts', fn ($q) => $q->where('cohorts.id', $cohortId));
        }

        $entries = $query->get();

        // Calculate ranks
        $rankedEntries = [];
        $currentRank = 1;
        $currentUserRank = null;
        $currentUserEntry = null;
        $currentUserId = Auth::id();

        foreach ($entries as $index => $entry) {
            $rank = $index + 1;
            $rankedEntries[] = [
                'rank' => $rank,
                'entry' => $entry,
                'is_current_user' => $entry->user_id === $currentUserId,
            ];

            if ($entry->user_id === $currentUserId) {
                $currentUserRank = $rank;
                $currentUserEntry = $entry;
            }
        }

        $cohorts = Cohort::orderBy('name')->get();

        return view('livewire.shared.leaderboard-page', [
            'rankedEntries' => $rankedEntries,
            'currentUserRank' => $currentUserRank,
            'currentUserEntry' => $currentUserEntry,
            'cohorts' => $cohorts,
        ]);
    }
}
