<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Concerns\RecordsAuditTrail;
use App\Filament\Concerns\StacksFormSections;
use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    use RecordsAuditTrail;
    use StacksFormSections;

    protected static string $resource = UserResource::class;

    protected function afterSave(): void
    {
        $this->audit('updated');
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->auditedDeleteAction()
                // Never delete yourself, and never delete a user who has
                // practice/submission history (it would cascade-delete it).
                // Deactivate the account instead.
                ->hidden(fn (User $record): bool => $record->is(auth()->user())
                    || $record->practiceAttempts()->exists()
                    || $record->submissions()->exists()),
        ];
    }
}
