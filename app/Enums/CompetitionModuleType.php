<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum CompetitionModuleType: string implements HasColor, HasLabel
{
    case Client = 'client';
    case Server = 'server';

    public function label(): string
    {
        return match ($this) {
            self::Client => 'Modul Client-Side',
            self::Server => 'Modul Server-Side',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Client => 'bg-pass/10 text-pass border-pass/20 font-bold',
            self::Server => 'bg-brand/10 text-brand-deep border-brand/20 font-bold',
        };
    }

    public function getLabel(): string
    {
        return $this->label();
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Client => 'info',
            self::Server => 'primary',
        };
    }
}
