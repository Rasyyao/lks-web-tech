<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'actor_id',
        'action',
        'subject_type',
        'subject_id',
        'meta',
        'ip',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    /**
     * Record an audit log entry.
     */
    public static function record(
        string $action,
        ?Model $subject = null,
        ?array $meta = null,
    ): self {
        return self::create([
            'actor_id' => auth()->id(),
            'action' => $action,
            'subject_type' => $subject ? $subject->getMorphClass() : null,
            'subject_id' => $subject?->getKey(),
            'meta' => $meta,
            'ip' => request()->ip(),
            'created_at' => now(),
        ]);
    }
}
