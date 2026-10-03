<?php

namespace App\Jobs;

use App\Services\Leaderboard\RecomputeLeaderboard as RecomputeLeaderboardService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class RecomputeLeaderboard implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $userId,
    ) {}

    public function handle(RecomputeLeaderboardService $service): void
    {
        $service->forUser($this->userId);
    }
}
