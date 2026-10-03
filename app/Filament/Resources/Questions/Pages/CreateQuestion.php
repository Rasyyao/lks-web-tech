<?php

namespace App\Filament\Resources\Questions\Pages;

use App\Filament\Concerns\RecordsAuditTrail;
use App\Filament\Resources\Questions\QuestionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateQuestion extends CreateRecord
{
    use RecordsAuditTrail;

    protected static string $resource = QuestionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->audit('created');
    }
}
