<?php

namespace App\Filament\Resources\Modules\Tables;

use App\Filament\Actions\AuditedDeleteAction;
use App\Enums\ModuleStatus;
use App\Enums\ModuleTrack;
use App\Models\AuditLog;
use App\Models\Module;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ModulesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Modul')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->description(fn (Module $record): string => $record->slug),
                TextColumn::make('track')
                    ->label('Jalur')
                    ->badge(),
                TextColumn::make('level')
                    ->label('Level')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
                TextColumn::make('opens_at')
                    ->label('Dibuka')
                    ->dateTime('d M Y H:i')
                    ->placeholder('—')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('closes_at')
                    ->label('Ditutup')
                    ->dateTime('d M Y H:i')
                    ->placeholder('—')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('cohorts.name')
                    ->label('Kelompok')
                    ->badge()
                    ->color('gray')
                    ->placeholder('Semua')
                    ->toggleable(),
                TextColumn::make('assets_count')
                    ->label('Aset')
                    ->counts('assets')
                    ->sortable(),
                TextColumn::make('submissions_count')
                    ->label('Pengumpulan')
                    ->counts('submissions')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('track')
                    ->label('Jalur')
                    ->options(ModuleTrack::class),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(ModuleStatus::class),
            ])
            ->recordActions([
                EditAction::make(),
                // Deleting would cascade-delete student submissions; archive instead.
                AuditedDeleteAction::make()
                    ->hidden(fn (Module $record): bool => $record->submissions()->exists())
                    ->modalDescription('Modul yang sudah memiliki pengumpulan tidak bisa dihapus — arsipkan saja.'),
                Action::make('publish')
                    ->label('Terbitkan')
                    ->icon('heroicon-o-eye')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalDescription('Siswa pada kelompok yang ditugaskan akan langsung melihat modul ini.')
                    ->visible(fn (Module $record): bool => $record->status !== ModuleStatus::Published)
                    ->action(fn (Module $record) => self::changeStatus($record, ModuleStatus::Published, 'module.published', 'Modul diterbitkan')),
                Action::make('archive')
                    ->label('Arsipkan')
                    ->icon('heroicon-o-archive-box')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalDescription('Modul disembunyikan dari siswa tetapi riwayat pengumpulan tetap tersimpan.')
                    ->visible(fn (Module $record): bool => $record->status === ModuleStatus::Published)
                    ->action(fn (Module $record) => self::changeStatus($record, ModuleStatus::Archived, 'module.archived', 'Modul diarsipkan')),
            ]);
    }

    private static function changeStatus(Module $record, ModuleStatus $status, string $auditAction, string $message): void
    {
        $record->update(['status' => $status]);

        AuditLog::record($auditAction, $record);

        Notification::make()
            ->title($message)
            ->success()
            ->send();
    }
}
