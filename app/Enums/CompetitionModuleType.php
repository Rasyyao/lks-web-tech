<?php

namespace App\Enums;

enum CompetitionModuleType: string
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
}
