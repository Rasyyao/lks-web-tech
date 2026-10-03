<?php

namespace App\Enums;

enum QuestionStatus: string
{
    case Draft = 'draft';
    case Review = 'review';
    case Published = 'published';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draf',
            self::Review => 'Menunggu review',
            self::Published => 'Terbit',
            self::Archived => 'Diarsipkan',
        };
    }
}
