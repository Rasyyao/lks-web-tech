<?php

namespace App\Filament\Resources\Topics\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
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
            ->defaultSort('position')
            ->reorderable('position')
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
