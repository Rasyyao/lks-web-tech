<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum SubmissionStatus: string implements HasColor, HasLabel
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

    public function getLabel(): string
    {
        return $this->label();
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Received => 'info',
            self::Graded => 'success',
            self::Rejected => 'danger',
        };
    }
}
