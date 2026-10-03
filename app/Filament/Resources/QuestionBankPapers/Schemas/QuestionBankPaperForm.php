<?php

namespace App\Filament\Resources\QuestionBankPapers\Schemas;

use App\Enums\CompetitionLevel;
use App\Enums\CompetitionModuleType;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class QuestionBankPaperForm
{
    public static function configure(Schema $schema): Schema
    {
        $currentYear = now()->year;
        $years = collect(range($currentYear + 1, 2015))->mapWithKeys(fn (int $y) => [$y => (string) $y])->all();

        return $schema
            ->components([
                Section::make('Kategori')
                    ->columns(3)
                    ->schema([
                        Select::make('year')
                            ->label('Tahun')
                            ->options($years)
                            ->default($currentYear)
                            ->required()
                            ->searchable()
                            ->native(false),
                        Select::make('level')
                            ->label('Jenjang')
                            ->options(CompetitionLevel::class)
                            ->required()
                            ->native(false),
                        Select::make('module_type')
                            ->label('Tipe modul')
                            ->options(CompetitionModuleType::class)
                            ->required()
                            ->native(false),
                    ]),

                Section::make('Berkas soal')
                    ->schema([
                        TextInput::make('title')
                            ->label('Judul')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(3)
                            ->maxLength(2000),
                        FileUpload::make('file_path')
                            ->label('Berkas PDF')
                            ->disk('private')
                            ->directory('question-bank')
                            ->visibility('private')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(10240)
                            ->required()
                            ->storeFileNamesIn('file_name')
                            ->helperText('Hanya PDF, maksimal 10 MB.'),
                    ]),
            ]);
    }
}
