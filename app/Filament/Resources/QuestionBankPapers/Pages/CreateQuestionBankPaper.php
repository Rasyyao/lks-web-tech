<?php

namespace App\Filament\Resources\QuestionBankPapers\Pages;

use App\Filament\Concerns\RecordsAuditTrail;
use App\Filament\Concerns\StacksFormSections;
use App\Filament\Resources\QuestionBankPapers\QuestionBankPaperResource;
use App\Models\QuestionBankPaper;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;

class CreateQuestionBankPaper extends CreateRecord
{
    use RecordsAuditTrail;
    use StacksFormSections;

    protected static string $resource = QuestionBankPaperResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['slug'] = QuestionBankPaper::generateUniqueSlug($data['title']);
        $data['file_name'] ??= basename($data['file_path']);
        $data['file_size'] = Storage::disk('private')->size($data['file_path']);
        $data['uploaded_by'] = auth()->id();

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->audit('uploaded');
    }
}
