<?php

namespace App\Filament\Resources\Questions\Tables;

use App\Enums\QuestionStatus;
use App\Enums\QuestionType;
use App\Filament\Actions\AuditedDeleteAction;
use App\Models\AuditLog;
use App\Models\PracticeAttemptItem;
use App\Models\Question;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class QuestionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['topic', 'author']))
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('topic.name')
                    ->label('Topik')
                    ->badge()
                    ->color('gray')
                    ->sortable(),
                TextColumn::make('body_md')
                    ->label('Pertanyaan')
                    ->limit(90)
                    ->wrap()
                    ->searchable(),
                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color('info'),
                TextColumn::make('difficulty')
                    ->label('Level')
                    ->sortable(),
                TextColumn::make('points')
                    ->label('Poin')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
                TextColumn::make('author.name')
                    ->label('Penulis')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Diubah')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                SelectFilter::make('topic_id')
                    ->label('Topik')
                    ->relationship('topic', 'name')
                    ->preload(),
                SelectFilter::make('type')
                    ->label('Tipe')
                    ->options(QuestionType::class),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(QuestionStatus::class),
                SelectFilter::make('difficulty')
                    ->label('Kesulitan')
                    ->options([1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5']),
            ])
            ->recordActions([
                EditAction::make(),
                // Practice history references questions; archive instead of delete.
                AuditedDeleteAction::make()
                    ->hidden(fn (Question $record): bool => PracticeAttemptItem::where('question_id', $record->id)->exists())
                    ->modalDescription('Soal yang sudah pernah dipakai latihan tidak bisa dihapus — arsipkan saja.'),
                Action::make('publish')
                    ->label('Terbitkan')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalDescription('Soal akan muncul di sesi latihan siswa.')
                    // Four-eyes rule: the author cannot publish their own question.
                    ->visible(fn (Question $record): bool => in_array($record->status, [QuestionStatus::Draft, QuestionStatus::Review], true)
                        && $record->created_by !== auth()->id())
                    ->action(function (Question $record): void {
                        $record->update([
                            'status' => QuestionStatus::Published,
                            'reviewed_by' => auth()->id(),
                        ]);

                        AuditLog::record('question.published', $record);

                        Notification::make()
                            ->title('Soal diterbitkan')
                            ->success()
                            ->send();
                    }),
            ]);
    }
}
