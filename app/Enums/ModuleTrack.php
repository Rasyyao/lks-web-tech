<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ModuleTrack: string implements HasColor, HasLabel
{
    case Client = 'client';
    case Server = 'server';
    case Fullstack = 'fullstack';

    public function label(): string
    {
        return match ($this) {
            self::Client => 'Client-side',
            self::Server => 'Server-side',
            self::Fullstack => 'Fullstack',
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
            self::Fullstack => 'warning',
        };
    }
}
