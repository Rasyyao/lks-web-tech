<?php

namespace App\Filament\Actions;

use App\Models\AuditLog;
use Filament\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Row delete action for admin tables that records who deleted the record.
 */
class AuditedDeleteAction extends DeleteAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->after(fn (Model $record) => AuditLog::record(
            Str::snake(class_basename($record)).'.deleted',
            $record,
        ));
    }
}
