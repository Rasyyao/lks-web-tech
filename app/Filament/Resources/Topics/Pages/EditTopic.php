<?php

namespace App\Filament\Resources\Topics\Pages;

use App\Filament\Concerns\RecordsAuditTrail;
use App\Filament\Concerns\StacksFormSections;
use App\Filament\Resources\Topics\TopicResource;
use Filament\Resources\Pages\EditRecord;

class EditTopic extends EditRecord
{
    use RecordsAuditTrail;
    use StacksFormSections;

    protected static string $resource = TopicResource::class;

    protected function afterSave(): void
    {
        $this->audit('updated');
    }
}
