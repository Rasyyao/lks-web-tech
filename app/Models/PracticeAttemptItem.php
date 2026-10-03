<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PracticeAttemptItem extends Model
{
    protected $fillable = [
        'attempt_id',
        'question_id',
        'question_version',
        'snapshot',
        'answer',
        'is_correct',
        'points',
        'position',
    ];

    protected function casts(): array
    {
        return [
            'snapshot' => 'array',
            'answer' => 'array',
            'is_correct' => 'boolean',
            'points' => 'integer',
            'question_version' => 'integer',
            'position' => 'integer',
        ];
    }

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(PracticeAttempt::class, 'attempt_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
