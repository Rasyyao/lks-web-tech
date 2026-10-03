<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum QuestionType: string implements HasLabel
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

    public function getLabel(): string
    {
        return $this->label();
    }
}
