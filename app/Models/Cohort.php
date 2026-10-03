<?php

namespace App\Models;

use App\Enums\CohortType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Cohort extends Model
{
    protected $fillable = [
        'name',
        'type',
        'year',
    ];

    protected function casts(): array
    {
        return [
            'type' => CohortType::class,
        ];
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function modules(): BelongsToMany
    {
        return $this->belongsToMany(Module::class, 'module_cohort');
    }

    public function announcements()
    {
        return $this->hasMany(Announcement::class);
    }
}
