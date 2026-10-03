<?php

namespace App\Filament\Resources\Cohorts\Tables;

use App\Filament\Actions\AuditedDeleteAction;
use App\Enums\CohortType;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CohortsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Jenis')
                    ->badge(),
                TextColumn::make('year')
                    ->label('Tahun')
                    ->sortable(),
                TextColumn::make('users_count')
                    ->label('Anggota')
                    ->counts('users')
                    ->sortable(),
                TextColumn::make('modules_count')
                    ->label('Modul')
                    ->counts('modules')
                    ->sortable(),
            ])
            ->defaultSort('year', 'desc')
            ->filters([
                SelectFilter::make('type')
                    ->label('Jenis')
                    ->options(CohortType::class),
                SelectFilter::make('year')
                    ->label('Tahun')
                    ->options(fn (): array => \App\Models\Cohort::query()
                        ->orderByDesc('year')
                        ->pluck('year', 'year')
                        ->all()),
            ])
            ->recordActions([
                EditAction::make(),
                AuditedDeleteAction::make()
                    ->modalDescription('Anggota tidak ikut terhapus, hanya keanggotaan kelompok ini. Modul & pengumuman yang terkait kelompok ini akan kehilangan penugasannya.'),
            ]);
    }
}
