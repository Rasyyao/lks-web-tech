<?php

namespace App\Enums;

enum SubmissionStatus: string
{
    case Received = 'received';
    case Graded = 'graded';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Received => 'Diterima',
            self::Graded => 'Sudah dinilai',
            self::Rejected => 'Ditolak',
        };
    }
}
