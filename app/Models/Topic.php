<?php

namespace App\Models;

use App\Enums\ModuleTrack;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Topic extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'track',
        'description',
        'position',
    ];

    protected function casts(): array
    {
        return [
            'track' => ModuleTrack::class,
            'position' => 'integer',
        ];
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function materials(): HasMany
    {
        return $this->hasMany(Material::class);
    }

    /**
     * Count published questions in this topic.
     */
    public function publishedQuestionCount(): int
    {
        return $this->questions()
            ->where('status', 'published')
            ->count();
    }
}
