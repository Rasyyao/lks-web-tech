<?php

namespace App\Enums;

enum QuestionType: string
{
    case MultipleChoice = 'multiple_choice';
    case TrueFalse = 'true_false';
    case ShortAnswer = 'short_answer';

    public function label(): string
    {
        return match ($this) {
            self::MultipleChoice => 'Pilihan ganda',
            self::TrueFalse => 'Benar/Salah',
            self::ShortAnswer => 'Jawaban singkat',
        };
    }
}
