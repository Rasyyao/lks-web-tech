<?php

namespace App\Filament\Resources\QuestionBankPapers\Pages;

use App\Filament\Concerns\RecordsAuditTrail;
use App\Filament\Concerns\StacksFormSections;
use App\Filament\Resources\QuestionBankPapers\QuestionBankPaperResource;
use App\Models\QuestionBankPaper;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditQuestionBankPaper extends EditRecord
{
    use RecordsAuditTrail;
    use StacksFormSections;

    protected static string $resource = QuestionBankPaperResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        /** @var QuestionBankPaper $paper */
        $paper = $this->getRecord();

        // A new PDF replaced the old one: remove the orphaned file and refresh the size.
        if (($data['file_path'] ?? $paper->file_path) !== $paper->file_path) {
            Storage::disk('private')->delete($paper->file_path);
            $data['file_size'] = Storage::disk('private')->size($data['file_path']);
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $this->audit('updated');
    }
}
