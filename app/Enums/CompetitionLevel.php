<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum CompetitionLevel: string implements HasColor, HasLabel
{
    case Kabupaten = 'kabupaten';
    case Provinsi = 'provinsi';
    case Nasional = 'nasional';

    public function label(): string
    {
        return match ($this) {
            self::Kabupaten => 'Tingkat Kabupaten / Kota',
            self::Provinsi => 'Tingkat Provinsi',
            self::Nasional => 'Tingkat Nasional',
        };
    }

    public function shortLabel(): string
    {
        return match ($this) {
            self::Kabupaten => 'Kabupaten/Kota',
            self::Provinsi => 'Provinsi',
            self::Nasional => 'Nasional',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Kabupaten => 'bg-paper text-ink border-rule',
            self::Provinsi => 'bg-gold/15 text-gold-text border-gold/30',
            self::Nasional => 'bg-tint text-brand-deep border-brand-deep/30 font-bold',
        };
    }

    public function getLabel(): string
    {
        return $this->label();
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Kabupaten => 'gray',
            self::Provinsi => 'warning',
            self::Nasional => 'primary',
        };
    }
}
