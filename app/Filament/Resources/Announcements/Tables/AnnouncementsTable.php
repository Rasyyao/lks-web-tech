<?php

namespace App\Filament\Resources\Announcements\Tables;

use App\Models\Announcement;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class AnnouncementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('cohort.name')
                    ->label('Untuk')
                    ->badge()
                    ->color('gray')
                    ->placeholder('Semua siswa'),
                TextColumn::make('published_at')
                    ->label('Terbit')
                    ->dateTime('d M Y H:i')
                    ->placeholder('Draf')
                    ->sortable(),
                TextColumn::make('state')
                    ->label('Status')
                    ->badge()
                    ->state(fn (Announcement $record): string => match (true) {
                        $record->published_at === null => 'Draf',
                        $record->published_at->isFuture() => 'Terjadwal',
                        default => 'Tayang',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'Tayang' => 'success',
                        'Terjadwal' => 'info',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('published_at', 'desc')
            ->filters([
                SelectFilter::make('cohort_id')
                    ->label('Kelompok')
                    ->relationship('cohort', 'name')
                    ->preload(),
                TernaryFilter::make('published')
                    ->label('Terbit')
                    ->placeholder('Semua')
                    ->trueLabel('Sudah terbit')
                    ->falseLabel('Draf')
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('published_at'),
                        false: fn ($query) => $query->whereNull('published_at'),
                    ),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
