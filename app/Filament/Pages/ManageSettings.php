<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use App\Models\User;
use App\Services\Leaderboard\RecomputeLeaderboard;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use UnitEnum;

class ManageSettings extends Page
{
    protected static ?string $navigationLabel = 'Pengaturan Poin';

    protected static ?string $title = 'Pengaturan Bobot Peringkat LKS';

    protected static string|UnitEnum|null $navigationGroup = 'Sistem';

    protected static ?int $navigationSort = 99;

    public int $weight_activity = 30;

    public int $weight_modules = 70;

    public int $daily_active_cap_minutes = 180;

    protected string $view = 'filament.pages.manage-settings';

    public function mount(): void
    {
        $this->weight_activity = (int) Setting::get('weight_activity', 30);
        $this->weight_modules = (int) Setting::get('weight_modules', 70);
        $this->daily_active_cap_minutes = (int) Setting::get('daily_active_cap_minutes', 180);
    }

    public function save(RecomputeLeaderboard $recompute): void
    {
        Setting::set('weight_activity', $this->weight_activity);
        Setting::set('weight_modules', $this->weight_modules);
        Setting::set('daily_active_cap_minutes', $this->daily_active_cap_minutes);

        $students = User::role('student')->get();
        foreach ($students as $student) {
            $recompute->forUser($student->id);
        }

        Notification::make()
            ->title('Pengaturan Disimpan')
            ->body('Bobot penilaian berhasil diperbarui dan seluruh peringkat telah dihitung ulang.')
            ->success()
            ->send();
    }
}
