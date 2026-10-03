<?php

namespace App\Filament\Resources\Topics\Pages;

use App\Filament\Concerns\RecordsAuditTrail;
use App\Filament\Concerns\StacksFormSections;
use App\Filament\Resources\Topics\TopicResource;
use App\Models\Topic;
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

    protected function getHeaderActions(): array
    {
        return [
            $this->auditedDeleteAction()
                // Deleting a topic cascades to its questions and attempt history.
                ->hidden(fn (Topic $record): bool => $record->questions()->exists())
                ->modalDescription('Materi dalam topik ini ikut terhapus.'),
        ];
    }
}
