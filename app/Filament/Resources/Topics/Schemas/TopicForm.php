<?php

namespace App\Filament\Resources\Topics\Schemas;

use App\Enums\ModuleTrack;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class TopicForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Topik')
                    ->description('Pengelompokan silabus, roadmap & materi latihan (Modul Client-Side atau Server-Side).')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama topik')
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
                            ->unique(ignoreRecord: true),
                        Select::make('track')
                            ->label('Jalur / Kategori Modul')
                            ->options([
                                ModuleTrack::Client->value => 'Client-side (HTML, CSS, JS, DOM, Canvas)',
                                ModuleTrack::Server->value => 'Server-side (PHP, Laravel, REST API, Vue/React + Axios)',
                            ])
                            ->default(ModuleTrack::Client->value)
                            ->required(),
                        Textarea::make('description')
                            ->label('Deskripsi / Ringkasan Silabus')
                            ->placeholder('Kompetensi dan ruang lingkup materi yang diujikan dalam topik ini...')
                            ->rows(2)
                            ->maxLength(500),
                        TextInput::make('position')
                            ->label('Urutan')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->required(),
                    ]),
            ]);
    }
}
