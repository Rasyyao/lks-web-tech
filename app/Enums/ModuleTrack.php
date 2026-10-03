<?php

namespace App\Enums;

enum ModuleTrack: string
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
}
