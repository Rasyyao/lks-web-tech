<?php

namespace App\Filament\Resources\Questions\Pages;

use App\Enums\QuestionStatus;
use App\Enums\QuestionType;
use App\Filament\Concerns\RecordsAuditTrail;
use App\Filament\Concerns\StacksFormSections;
use App\Filament\Resources\Questions\QuestionResource;
use App\Models\Question;
use Filament\Resources\Pages\EditRecord;

class EditQuestion extends EditRecord
{
    use RecordsAuditTrail;
    use StacksFormSections;

    protected static string $resource = QuestionResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        /** @var Question $question */
        $question = $this->getRecord();

        // Bump the version whenever the question text changes.
        if (($data['body_md'] ?? $question->body_md) !== $question->body_md) {
            $data['version'] = $question->version + 1;
        }

        // Record who approved the question the moment it is published.
        $newStatus = $data['status'] instanceof QuestionStatus
            ? $data['status']
            : QuestionStatus::tryFrom((string) $data['status']);

        if ($newStatus === QuestionStatus::Published && $question->status !== QuestionStatus::Published) {
            $data['reviewed_by'] = auth()->id();
        }

        return $data;
    }

    protected function afterSave(): void
    {
        /** @var Question $question */
        $question = $this->getRecord();

        // Drop rows that don't belong to the (possibly changed) question type.
        if ($question->type === QuestionType::ShortAnswer) {
            $question->options()->delete();
        } else {
            $question->answers()->delete();
        }

        $this->audit('updated');
    }
}
