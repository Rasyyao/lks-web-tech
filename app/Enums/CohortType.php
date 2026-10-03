<?php

namespace App\Enums;

enum CohortType: string
{
    case ClassGroup = 'class';
    case Selection = 'selection';

    public function label(): string
    {
        return match ($this) {
            self::ClassGroup => 'Kelas',
            self::Selection => 'Seleksi',
        };
    }
}
