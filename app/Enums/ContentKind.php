<?php

namespace App\Enums;

enum ContentKind: string
{
    case Single = 'single';
    case Recurring = 'recurring';

    public function label(): string
    {
        return match ($this) {
            self::Single => 'Single',
            self::Recurring => 'Recurring',
        };
    }
}
