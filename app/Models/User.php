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

    /**
     * Check if user belongs to any of the given cohorts.
     */
    public function belongsToCohort(int ...$cohortIds): bool
    {
        return $this->cohorts()->whereIn('cohorts.id', $cohortIds)->exists();
    }
}
