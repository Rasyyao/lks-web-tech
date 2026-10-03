<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionAnswer extends Model
{
    protected $fillable = [
        'question_id',
        'text',
        'normalized',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Normalize an answer string for comparison:
     * trim, lowercase, collapse whitespace.
     */
    public static function normalize(string $text): string
    {
        return mb_strtolower(preg_replace('/\s+/', ' ', trim($text)));
    }
}
