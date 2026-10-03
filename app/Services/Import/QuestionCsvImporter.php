<?php

namespace App\Services\Import;

use App\Enums\QuestionStatus;
use App\Enums\QuestionType;
use App\Models\Question;
use App\Models\QuestionAnswer;
use App\Models\QuestionOption;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Support\Str;

class QuestionCsvImporter
{
    /**
     * Import questions from CSV file.
     *
     * @param string $csvPath Full path to CSV file
     * @param User $creator User importing the questions
     * @return array{
     *     created: int,
     *     skipped: int,
     *     failed: array<array{row: int, reason: string}>
     * }
     */
    public function import(string $csvPath, User $creator): array
    {
        $created = 0;
        $skipped = 0;
        $failed = [];

        if (! file_exists($csvPath) || ! ($handle = fopen($csvPath, 'r'))) {
            return [
                'created' => 0,
                'skipped' => 0,
                'failed' => [['row' => 0, 'reason' => 'Berkas CSV tidak dapat dibaca.']],
            ];
        }

        $header = fgetcsv($handle);
        if (! $header) {
            fclose($handle);
            return [
                'created' => 0,
                'skipped' => 0,
                'failed' => [['row' => 1, 'reason' => 'Berkas CSV kosong.']],
            ];
        }

        $header = array_map(fn ($col) => strtolower(trim((string) $col)), $header);
        $colMap = array_flip($header);

        $requiredCols = ['topic', 'type', 'body'];
        foreach ($requiredCols as $req) {
            if (! isset($colMap[$req])) {
                fclose($handle);
                return [
                    'created' => 0,
                    'skipped' => 0,
                    'failed' => [['row' => 1, 'reason' => "Kolom wajib \"{$req}\" tidak ditemukan pada judul CSV."]],
                ];
            }
        }

        $rowNum = 1;
        while (($row = fgetcsv($handle)) !== false) {
            $rowNum++;

            if (empty(array_filter($row))) {
                continue;
            }

            $getVal = fn ($col, $default = '') => isset($colMap[$col], $row[$colMap[$col]])
                ? trim((string) $row[$colMap[$col]])
                : $default;

            $topicName = $getVal('topic');
            $typeRaw = strtolower($getVal('type'));
            $body = $getVal('body');
            $difficulty = max(1, min(5, (int) $getVal('difficulty', '1')));
            $points = max(1, (int) $getVal('points', '10'));
            $explanation = $getVal('explanation');
            $correct = strtolower($getVal('correct'));

            if ($topicName === '' || $body === '') {
                $failed[] = ['row' => $rowNum, 'reason' => 'Topik atau teks soal kosong.'];
                continue;
            }

            // Map type
            $type = match ($typeRaw) {
                'multiple_choice', 'mc', 'pilihan_ganda' => QuestionType::MultipleChoice,
                'true_false', 'tf', 'benar_salah' => QuestionType::TrueFalse,
                'short_answer', 'sa', 'isian_singkat' => QuestionType::ShortAnswer,
                default => null,
            };

            if (! $type) {
                $failed[] = ['row' => $rowNum, 'reason' => "Tipe soal \"{$typeRaw}\" tidak valid."];
                continue;
            }

            // Topic find or create
            $topic = Topic::firstOrCreate(
                ['slug' => Str::slug($topicName)],
                ['name' => $topicName, 'position' => Topic::max('position') + 1]
            );

            // Validation per type
            if ($type === QuestionType::MultipleChoice) {
                $optA = $getVal('option_a');
                $optB = $getVal('option_b');
                if ($optA === '' || $optB === '') {
                    $failed[] = ['row' => $rowNum, 'reason' => 'Pilihan A dan B wajib diisi untuk pilihan ganda.'];
                    continue;
                }
                if (! in_array($correct, ['a', 'b', 'c', 'd'], true)) {
                    $failed[] = ['row' => $rowNum, 'reason' => 'Kunci jawaban pilihan ganda harus berupa A, B, C, atau D.'];
                    continue;
                }
            } elseif ($type === QuestionType::TrueFalse) {
                if (! in_array($correct, ['true', 'false', 'benar', 'salah', '1', '0'], true)) {
                    $failed[] = ['row' => $rowNum, 'reason' => 'Kunci jawaban benar/salah harus berupa "true" atau "false".'];
                    continue;
                }
            } elseif ($type === QuestionType::ShortAnswer) {
                $accepted = $getVal('accepted_answers');
                if ($accepted === '' && $correct === '') {
                    $failed[] = ['row' => $rowNum, 'reason' => 'Jawaban yang diterima (accepted_answers) wajib diisi untuk isian singkat.'];
                    continue;
                }
            }

            // Create question
            $question = Question::create([
                'topic_id' => $topic->id,
                'type' => $type,
                'body_md' => $body,
                'difficulty' => $difficulty,
                'points' => $points,
                'explanation_md' => $explanation ?: null,
                'status' => QuestionStatus::Published,
                'version' => 1,
                'created_by' => $creator->id,
                'reviewed_by' => $creator->id,
            ]);

            // Options / Answers
            if ($type === QuestionType::MultipleChoice) {
                $optionsData = [
                    'a' => $getVal('option_a'),
                    'b' => $getVal('option_b'),
                    'c' => $getVal('option_c'),
                    'd' => $getVal('option_d'),
                ];
                $pos = 1;
                foreach ($optionsData as $key => $label) {
                    if ($label !== '') {
                        QuestionOption::create([
                            'question_id' => $question->id,
                            'label' => $label,
                            'is_correct' => ($key === $correct),
                            'position' => $pos++,
                        ]);
                    }
                }
            } elseif ($type === QuestionType::TrueFalse) {
                $isTrueCorrect = in_array($correct, ['true', 'benar', '1'], true);
                QuestionOption::create([
                    'question_id' => $question->id,
                    'label' => 'Benar',
                    'is_correct' => $isTrueCorrect,
                    'position' => 1,
                ]);
                QuestionOption::create([
                    'question_id' => $question->id,
                    'label' => 'Salah',
                    'is_correct' => ! $isTrueCorrect,
                    'position' => 2,
                ]);
            } elseif ($type === QuestionType::ShortAnswer) {
                $rawList = $getVal('accepted_answers') ?: $correct;
                $answers = array_filter(array_map('trim', explode('|', $rawList)));
                foreach ($answers as $ans) {
                    QuestionAnswer::create([
                        'question_id' => $question->id,
                        'text' => $ans,
                        'normalized' => QuestionAnswer::normalize($ans),
                    ]);
                }
            }

            $created++;
        }

        fclose($handle);

        return [
            'created' => $created,
            'skipped' => $skipped,
            'failed' => $failed,
        ];
    }
}
