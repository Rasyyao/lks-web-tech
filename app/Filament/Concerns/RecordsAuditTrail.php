<?php

namespace App\Filament\Concerns;

use App\Models\AuditLog;
use Filament\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Writes admin-panel changes to the audit log (PRD: "who changed what").
 *
 * Only the *names* of changed attributes are stored, never their values, so
 * secrets such as password hashes never end up in the log.
 */
trait RecordsAuditTrail
{
    /** Attributes that must never appear in audit metadata. */
    protected array $auditHidden = ['password', 'remember_token'];

    protected function audit(string $verb, ?Model $record = null, array $extra = []): void
    {
        $record ??= $this->getRecord();

        $changed = collect(array_keys($record->getChanges()))
            ->reject(fn (string $key) => in_array($key, $this->auditHidden, true) || $key === 'updated_at')
            ->values()
            ->all();

        AuditLog::record(
            Str::snake(class_basename($record)).'.'.$verb,
            $record,
            array_filter(['changed' => $changed] + $extra),
        );
    }

    /**
     * A DeleteAction that records who deleted the record.
     */
    protected function auditedDeleteAction(): DeleteAction
    {
        return DeleteAction::make()
            ->after(fn (Model $record) => AuditLog::record(
                Str::snake(class_basename($record)).'.deleted',
                $record,
            ));
    }
}
