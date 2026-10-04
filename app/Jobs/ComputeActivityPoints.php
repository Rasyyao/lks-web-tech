<?php

namespace App\Jobs;

use App\Services\Leaderboard\RecomputeLeaderboard;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ComputeActivityPoints implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $userId,
    ) {}

    public function handle(RecomputeLeaderboard $service): void
    {
        $service->forUser($this->userId);
    }
}
