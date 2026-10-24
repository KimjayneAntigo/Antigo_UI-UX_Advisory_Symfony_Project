<?php

namespace App\Enum;

enum MessageRole: string
{
    case CLIENT = 'client';
    case DESIGNER = 'designer';

    public function label(): string
    {
        return match ($this) {
            self::CLIENT => 'Client',
            self::DESIGNER => 'Designer',
        };
    }
}
