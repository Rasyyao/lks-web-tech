<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active && $this->hasRole('admin');
    }

    /**
     * Where this user lands by default: admins in the admin panel, mentors in
     * the submission inbox, students on their dashboard.
     */
    public function homeUrl(): string
    {
        return match (true) {
            $this->hasRole('admin') => url('/admin'),
            $this->hasRole('mentor') => route('mentor.submissions'),
            default => route('dashboard'),
        };
    }

    /**
     * Whether a previously requested ("intended") path is one this user may open,
     * so a redirect after login never ends in a 403.
     */
    public function canVisitPath(string $path): bool
    {
        $path = trim($path, '/');

        if ($path === 'admin' || str_starts_with($path, 'admin/')) {
            return $this->canAccessPanel(filament()->getPanel('admin'));
        }

        if ($path === 'mentor' || str_starts_with($path, 'mentor/')) {
            return $this->hasAnyRole(['mentor', 'admin']);
        }

        foreach (['beranda', 'modul', 'latihan', 'materi'] as $studentArea) {
            if ($path === $studentArea || str_starts_with($path, $studentArea.'/')) {
                return $this->hasRole('student');
            }
        }

        return true;
    }

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'is_active',
        'must_change_password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
            'must_change_password' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    public function cohorts(): BelongsToMany
    {
        return $this->belongsToMany(Cohort::class)->withTimestamps();
    }

    public function practiceAttempts(): HasMany
    {
        return $this->hasMany(PracticeAttempt::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function leaderboardEntry(): HasOne
    {
        return $this->hasOne(LeaderboardEntry::class);
    }

    public function levelProgress(): HasMany
    {
        return $this->hasMany(LevelProgress::class);
    }

    public function dailyActivities(): HasMany
    {
        return $this->hasMany(DailyActivity::class);
    }

    public function activityEvents(): HasMany
    {
        return $this->hasMany(ActivityEvent::class);
    }

    public function exerciseAttempts(): HasMany
    {
        return $this->hasMany(ExerciseAttempt::class);
    }

    /**
     * Check if user belongs to any of the given cohorts.
     */
    public function belongsToCohort(int ...$cohortIds): bool
    {
        return $this->cohorts()->whereIn('cohorts.id', $cohortIds)->exists();
    }
}
