<?php

namespace App\Filament\Resources\QuestionBankPapers\Pages;

use App\Filament\Resources\QuestionBankPapers\QuestionBankPaperResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListQuestionBankPapers extends ListRecords
{
    protected static string $resource = QuestionBankPaperResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
