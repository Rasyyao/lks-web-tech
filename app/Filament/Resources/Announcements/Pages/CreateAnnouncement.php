<?php

namespace App\Filament\Resources\Announcements\Pages;

use App\Filament\Concerns\RecordsAuditTrail;
use App\Filament\Resources\Announcements\AnnouncementResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAnnouncement extends CreateRecord
{
    use RecordsAuditTrail;

    protected static string $resource = AnnouncementResource::class;

    protected function afterCreate(): void
    {
        $this->audit('created');
    }
}
