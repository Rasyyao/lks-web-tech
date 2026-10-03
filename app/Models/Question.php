<?php

namespace App\Models;

use App\Enums\QuestionStatus;
use App\Enums\QuestionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    protected $fillable = [
        'topic_id',
        'type',
        'body_md',
        'difficulty',
        'points',
        'explanation_md',
        'status',
        'version',
        'created_by',
        'reviewed_by',
    ];

    protected function casts(): array
    {
        return [
            'type' => QuestionType::class,
            'status' => QuestionStatus::class,
            'difficulty' => 'integer',
            'points' => 'integer',
            'version' => 'integer',
        ];
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(QuestionOption::class)->orderBy('position');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(QuestionAnswer::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Build a snapshot of the question for an attempt item.
     * Includes question body, options (without correct marker), and metadata.
     */
    public function buildSnapshot(): array
    {
        $snapshot = [
            'body_md' => $this->body_md,
            'type' => $this->type->value,
            'difficulty' => $this->difficulty,
            'points' => $this->points,
            'explanation_md' => $this->explanation_md,
        ];

        if ($this->type === QuestionType::MultipleChoice || $this->type === QuestionType::TrueFalse) {
            $snapshot['options'] = $this->options->map(fn ($o) => [
                'id' => $o->id,
                'label' => $o->label,
                'is_correct' => $o->is_correct,
                'position' => $o->position,
            ])->all();
        }

        if ($this->type === QuestionType::ShortAnswer) {
            $snapshot['accepted_answers'] = $this->answers->map(fn ($a) => [
                'text' => $a->text,
                'normalized' => $a->normalized,
            ])->all();
        }

        return $snapshot;
    }
}
