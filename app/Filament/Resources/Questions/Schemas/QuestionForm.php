<?php

namespace App\Filament\Resources\Questions\Schemas;

use App\Enums\QuestionStatus;
use App\Enums\QuestionType;
use App\Models\Question;
use App\Models\QuestionAnswer;
use Closure;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class QuestionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Soal')
                    ->schema([
                        Select::make('topic_id')
                            ->label('Topik')
                            ->relationship('topic', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('type')
                            ->label('Tipe soal')
                            ->options(QuestionType::class)
                            ->default(QuestionType::MultipleChoice)
                            ->required()
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(function (mixed $state, Set $set): void {
                                // Pre-fill the two fixed options for true/false questions.
                                if (self::typeOf($state) === QuestionType::TrueFalse) {
                                    $set('options', [
                                        ['label' => 'Benar', 'is_correct' => true],
                                        ['label' => 'Salah', 'is_correct' => false],
                                    ]);
                                }
                            }),
                        Select::make('difficulty')
                            ->label('Kesulitan')
                            ->options([
                                1 => '1 — Sangat mudah',
                                2 => '2 — Mudah',
                                3 => '3 — Sedang',
                                4 => '4 — Sulit',
                                5 => '5 — Sangat sulit',
                            ])
                            ->default(1)
                            ->required()
                            ->native(false),
                        TextInput::make('points')
                            ->label('Poin')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(1000)
                            ->default(10)
                            ->required(),
                        MarkdownEditor::make('body_md')
                            ->label('Pertanyaan (Markdown, blok kode diperbolehkan)')
                            ->required(),
                    ]),

                Section::make('Pilihan jawaban')
                    ->description('Tandai tepat satu pilihan sebagai jawaban benar.')
                    ->visible(fn (Get $get): bool => in_array(
                        self::typeOf($get('type')),
                        [QuestionType::MultipleChoice, QuestionType::TrueFalse],
                        true,
                    ))
                    ->schema([
                        Repeater::make('options')
                            ->hiddenLabel()
                            ->relationship('options')
                            ->orderColumn('position')
                            ->reorderable()
                            ->minItems(2)
                            ->maxItems(fn (Get $get): int => self::typeOf($get('type')) === QuestionType::TrueFalse ? 2 : 6)
                            ->addActionLabel('Tambah pilihan')
                            ->defaultItems(4)
                            ->schema([
                                TextInput::make('label')
                                    ->label('Teks pilihan')
                                    ->required()
                                    ->maxLength(255),
                                Toggle::make('is_correct')
                                    ->label('Jawaban benar')
                                    ->inline(false)
                                    ->default(false),
                            ])
                            ->rules([
                                fn (): Closure => function (string $attribute, mixed $value, Closure $fail): void {
                                    $correct = collect($value)->where('is_correct', true)->count();

                                    if ($correct !== 1) {
                                        $fail('Harus ada tepat satu jawaban yang benar.');
                                    }
                                },
                            ]),
                    ]),

                Section::make('Jawaban yang diterima')
                    ->description('Perbandingan tidak peka huruf besar/kecil dan spasi berlebih diabaikan.')
                    ->visible(fn (Get $get): bool => self::typeOf($get('type')) === QuestionType::ShortAnswer)
                    ->schema([
                        Repeater::make('answers')
                            ->hiddenLabel()
                            ->relationship('answers')
                            ->minItems(1)
                            ->defaultItems(1)
                            ->addActionLabel('Tambah jawaban alternatif')
                            ->schema([
                                TextInput::make('text')
                                    ->label('Jawaban')
                                    ->required()
                                    ->maxLength(255),
                            ])
                            ->mutateRelationshipDataBeforeCreateUsing(fn (array $data): array => self::withNormalized($data))
                            ->mutateRelationshipDataBeforeSaveUsing(fn (array $data): array => self::withNormalized($data)),
                    ]),

                Section::make('Pembahasan & status')
                    ->schema([
                        MarkdownEditor::make('explanation_md')
                            ->label('Pembahasan (Markdown)'),
                        Select::make('status')
                            ->label('Status')
                            ->options(QuestionStatus::class)
                            ->default(QuestionStatus::Draft)
                            ->required()
                            ->native(false)
                            // PRD: the author cannot publish their own question — a
                            // different admin/mentor must review & publish it.
                            ->disableOptionWhen(fn (string $value, ?Question $record): bool => $value === QuestionStatus::Published->value
                                && $record?->status !== QuestionStatus::Published
                                && (! $record || $record->created_by === auth()->id()))
                            ->helperText('Soal tidak bisa diterbitkan oleh penulisnya sendiri. Pilih "Menunggu review" agar admin/mentor lain menerbitkannya.'),
                    ]),
            ]);
    }

    private static function typeOf(mixed $state): ?QuestionType
    {
        return $state instanceof QuestionType ? $state : QuestionType::tryFrom((string) $state);
    }

    private static function withNormalized(array $data): array
    {
        $data['normalized'] = QuestionAnswer::normalize((string) ($data['text'] ?? ''));

        return $data;
    }
}
