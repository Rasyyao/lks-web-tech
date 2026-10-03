<?php

namespace App\Filament\Resources\QuestionBankPapers;

use App\Filament\Resources\QuestionBankPapers\Pages\CreateQuestionBankPaper;
use App\Filament\Resources\QuestionBankPapers\Pages\EditQuestionBankPaper;
use App\Filament\Resources\QuestionBankPapers\Pages\ListQuestionBankPapers;
use App\Filament\Resources\QuestionBankPapers\Schemas\QuestionBankPaperForm;
use App\Filament\Resources\QuestionBankPapers\Tables\QuestionBankPapersTable;
use App\Models\QuestionBankPaper;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class QuestionBankPaperResource extends Resource
{
    protected static ?string $model = QuestionBankPaper::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string|UnitEnum|null $navigationGroup = 'Modul Lomba';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'soal lomba';

    protected static ?string $pluralModelLabel = 'Bank soal lomba';

    protected static ?string $navigationLabel = 'Bank soal lomba';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return QuestionBankPaperForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QuestionBankPapersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQuestionBankPapers::route('/'),
            'create' => CreateQuestionBankPaper::route('/create'),
            'edit' => EditQuestionBankPaper::route('/{record}/edit'),
        ];
    }
}
