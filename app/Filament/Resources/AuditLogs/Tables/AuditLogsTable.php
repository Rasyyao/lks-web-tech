<?php

namespace App\Filament\Resources\AuditLogs\Tables;

use App\Models\AuditLog;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AuditLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('actor'))
            ->columns([
                TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y H:i:s')
                    ->sortable(),
                TextColumn::make('actor.name')
                    ->label('Pelaku')
                    ->placeholder('Sistem')
                    ->searchable(),
                TextColumn::make('action')
                    ->label('Aksi')
                    ->badge()
                    ->searchable(),
                TextColumn::make('subject')
                    ->label('Objek')
                    ->state(fn (AuditLog $record): ?string => $record->subject_type
                        ? class_basename($record->subject_type).' #'.$record->subject_id
                        : null)
                    ->placeholder('—'),
                TextColumn::make('meta')
                    ->label('Detail')
                    ->state(fn (AuditLog $record): ?string => $record->meta
                        ? json_encode($record->meta, JSON_UNESCAPED_UNICODE)
                        : null)
                    ->limit(60)
                    ->tooltip(fn (AuditLog $record): ?string => $record->meta
                        ? json_encode($record->meta, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
                        : null)
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('ip')
                    ->label('IP')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('action')
                    ->label('Aksi')
                    ->options(fn (): array => AuditLog::query()
                        ->distinct()
                        ->orderBy('action')
                        ->pluck('action', 'action')
                        ->all())
                    ->searchable(),
                SelectFilter::make('actor_id')
                    ->label('Pelaku')
                    ->relationship('actor', 'name')
                    ->searchable()
                    ->preload(),
            ]);
    }
}
