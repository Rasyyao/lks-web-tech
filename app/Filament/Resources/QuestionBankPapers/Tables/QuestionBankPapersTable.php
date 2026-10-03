<?php

namespace App\Filament\Resources\QuestionBankPapers\Tables;

use App\Enums\CompetitionLevel;
use App\Enums\CompetitionModuleType;
use App\Models\QuestionBankPaper;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class QuestionBankPapersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('uploader'))
            ->columns([
                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->wrap()
                    ->description(fn (QuestionBankPaper $record): string => $record->file_name),
                TextColumn::make('year')
                    ->label('Tahun')
                    ->sortable(),
                TextColumn::make('level')
                    ->label('Jenjang')
                    ->badge()
                    ->formatStateUsing(fn ($state): string => $state->shortLabel()),
                TextColumn::make('module_type')
                    ->label('Modul')
                    ->badge(),
                TextColumn::make('formatted_size')
                    ->label('Ukuran'),
                TextColumn::make('download_count')
                    ->label('Unduhan')
                    ->sortable(),
                TextColumn::make('uploader.name')
                    ->label('Pengunggah')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Diunggah')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('year', 'desc')
            ->filters([
                SelectFilter::make('year')
                    ->label('Tahun')
                    ->options(fn (): array => QuestionBankPaper::query()
                        ->orderByDesc('year')
                        ->pluck('year', 'year')
                        ->all()),
                SelectFilter::make('level')
                    ->label('Jenjang')
                    ->options(CompetitionLevel::class),
                SelectFilter::make('module_type')
                    ->label('Tipe modul')
                    ->options(CompetitionModuleType::class),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('download')
                    ->label('Unduh')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (QuestionBankPaper $record): string => route('download.question-paper', $record))
                    ->openUrlInNewTab(),
            ]);
    }
}
