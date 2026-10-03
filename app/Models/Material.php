<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Material extends Model
{
    protected $fillable = [
        'topic_id',
        'title',
        'body_md',
        'level',
        'position',
    ];

    protected function casts(): array
    {
        return [
            'level' => 'integer',
            'position' => 'integer',
        ];
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }
}
