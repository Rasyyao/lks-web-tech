<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RoadmapSection extends Model
{
    protected $fillable = [
        'roadmap_page_id',
        'slug',
        'kind',
        'title',
        'position',
        'body_md',
        'body_html',
        'hash',
    ];

    protected function casts(): array
    {
        return [
            'position' => 'integer',
        ];
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(RoadmapPage::class, 'roadmap_page_id');
    }

    public function reads(): HasMany
    {
        return $this->hasMany(SectionRead::class, 'roadmap_section_id');
    }
}
