<?php

namespace App\Filament\Widgets;

use App\Enums\ModuleStatus;
use App\Enums\QuestionStatus;
use App\Enums\SubmissionStatus;
use App\Models\Module;
use App\Models\Question;
use App\Models\Submission;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PlatformStats extends StatsOverviewWidget
{
    protected ?string $heading = 'Ringkasan platform';

    protected function getStats(): array
    {
        return [
            Stat::make('Siswa aktif', User::role('student')->where('is_active', true)->count())
                ->icon('heroicon-o-academic-cap')
                ->description('Akun siswa yang bisa masuk'),
            Stat::make('Modul terbit', Module::where('status', ModuleStatus::Published)->count())
                ->icon('heroicon-o-cube-transparent')
                ->description(Module::where('status', ModuleStatus::Draft)->count().' draf'),
            Stat::make('Soal terbit', Question::where('status', QuestionStatus::Published)->count())
                ->icon('heroicon-o-question-mark-circle')
                ->description(Question::where('status', QuestionStatus::Review)->count().' menunggu review'),
            Stat::make('Pengumpulan belum dinilai', Submission::where('status', SubmissionStatus::Received)->count())
                ->icon('heroicon-o-inbox-arrow-down')
                ->color('warning')
                ->description('Menunggu review mentor'),
        ];
    }
}
