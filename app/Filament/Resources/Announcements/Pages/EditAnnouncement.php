<?php

namespace App\Filament\Resources\Announcements\Pages;

use App\Filament\Concerns\RecordsAuditTrail;
use App\Filament\Concerns\StacksFormSections;
use App\Filament\Resources\Announcements\AnnouncementResource;
use Filament\Resources\Pages\EditRecord;

class EditAnnouncement extends EditRecord
{
    use RecordsAuditTrail;
    use StacksFormSections;

    protected static string $resource = AnnouncementResource::class;

    protected function afterSave(): void
    {
        $this->audit('updated');
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->auditedDeleteAction(),
        ];
    }
}
