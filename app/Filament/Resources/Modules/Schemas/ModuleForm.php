<?php

namespace App\Filament\Resources\Modules\Schemas;

use App\Enums\ModuleStatus;
use App\Enums\ModuleTrack;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ModuleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi modul')
                    ->schema([
                        TextInput::make('title')
                            ->label('Judul modul')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, ?string $state, Set $set): void {
                                if ($operation === 'create') {
                                    $set('slug', Str::slug((string) $state));
                                }
                            }),
                        TextInput::make('slug')
                            ->required()
                            ->alphaDash()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            // The automated judge and URLs key off the slug, so it is
                            // locked once the module exists.
                            ->disabled(fn (string $operation): bool => $operation === 'edit')
                            ->helperText(fn (string $operation): ?string => $operation === 'edit'
                                ? 'Slug dipakai penilaian otomatis & URL — tidak dapat diubah.'
                                : null),
                        Select::make('track')
                            ->label('Jalur')
                            ->options(ModuleTrack::class)
                            ->default(ModuleTrack::Fullstack)
                            ->required()
                            ->native(false),
                        Select::make('level')
                            ->label('Level')
                            ->options([1 => 'Level 1', 2 => 'Level 2', 3 => 'Level 3', 4 => 'Level 4', 5 => 'Level 5'])
                            ->default(1)
                            ->required()
                            ->native(false),
                        Select::make('status')
                            ->label('Status')
                            ->options(ModuleStatus::class)
                            ->default(ModuleStatus::Draft)
                            ->required()
                            ->native(false)
                            ->helperText('Siswa hanya melihat modul berstatus Terbit.'),
                        Textarea::make('summary')
                            ->label('Ringkasan')
                            ->rows(2)
                            ->maxLength(1000),
                    ]),

                Section::make('Jadwal & batas')
                    ->schema([
                        DateTimePicker::make('opens_at')
                            ->label('Dibuka')
                            ->seconds(false),
                        DateTimePicker::make('closes_at')
                            ->label('Ditutup')
                            ->seconds(false)
                            ->after('opens_at'),
                        TextInput::make('duration_minutes')
                            ->label('Durasi pengerjaan (menit)')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(1440),
                        TextInput::make('max_attempts_per_day')
                            ->label('Maks. pengumpulan per hari')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(50)
                            ->default(5)
                            ->required(),
                        Select::make('cohorts')
                            ->label('Kelompok yang mendapat modul ini')
                            ->relationship('cohorts', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->helperText('Kosong = terlihat oleh semua siswa.'),
                    ]),

                Section::make('Konten')
                    ->schema([
                        MarkdownEditor::make('brief_md')
                            ->label('Brief / soal (Markdown)')
                            ->minHeight('18rem'),
                        MarkdownEditor::make('rules_md')
                            ->label('Aturan & penilaian (Markdown)'),
                    ]),
            ]);
    }
}
