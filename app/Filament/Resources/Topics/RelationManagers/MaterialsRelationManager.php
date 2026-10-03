<?php

namespace App\Filament\Resources\Topics\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MaterialsRelationManager extends RelationManager
{
    protected static string $relationship = 'materials';

    protected static ?string $title = 'Materi';

    protected static ?string $modelLabel = 'materi';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                TextInput::make('title')
                    ->label('Judul')
                    ->required()
                    ->maxLength(255)
                    ->columnSpan(3),
                Select::make('level')
                    ->label('Level')
                    ->options([1 => 'Level 1', 2 => 'Level 2', 3 => 'Level 3', 4 => 'Level 4', 5 => 'Level 5'])
                    ->default(1)
                    ->required()
                    ->native(false),
                TextInput::make('position')
                    ->label('Urutan')
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->required(),
                MarkdownEditor::make('body_md')
                    ->label('Isi materi (Markdown)')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('position')
                    ->label('#')
                    ->sortable(),
                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable(),
                TextColumn::make('level')
                    ->label('Level')
                    ->badge()
                    ->formatStateUsing(fn ($state): string => "Level {$state}")
                    ->sortable(),
            ])
            ->defaultSort('position')
            ->reorderable('position')
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
