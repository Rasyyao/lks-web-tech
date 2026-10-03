<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum CohortType: string implements HasColor, HasLabel
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

    public function getLabel(): string
    {
        return $this->label();
    }

    public function getColor(): string
    {
        return match ($this) {
            self::ClassGroup => 'info',
            self::Selection => 'primary',
        };
    }
}
