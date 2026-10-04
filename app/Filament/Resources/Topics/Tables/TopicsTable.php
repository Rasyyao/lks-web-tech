<?php

namespace App\Filament\Resources\Topics\Tables;

use App\Enums\ModuleTrack;
use App\Filament\Actions\AuditedDeleteAction;
use App\Models\Topic;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TopicsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('position')
                    ->label('#')
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Topik')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('track')
                    ->label('Jalur')
                    ->badge()
                    ->sortable(),
                TextColumn::make('slug')
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('questions_count')
                    ->label('Soal')
                    ->counts('questions')
                    ->sortable(),
                TextColumn::make('materials_count')
                    ->label('Materi')
                    ->counts('materials')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('track')
                    ->label('Jalur Modul')
                    ->options([
                        ModuleTrack::Client->value => 'Client-side',
                        ModuleTrack::Server->value => 'Server-side',
                    ]),
            ])
            ->defaultSort('position')
            ->reorderable('position')
            ->recordActions([
                EditAction::make(),
                // Deleting a topic cascades to its questions and attempt history.
                AuditedDeleteAction::make()
                    ->hidden(fn (Topic $record): bool => $record->questions()->exists())
                    ->modalDescription('Materi dalam topik ini ikut terhapus.'),
            ]);
    }
}
