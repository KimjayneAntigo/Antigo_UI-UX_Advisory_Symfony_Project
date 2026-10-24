<?php

namespace App\Enum;

enum ProjectStatus: string
{
    case PENDING = 'pending';
    case IN_DESIGN = 'in_design';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::IN_DESIGN => 'In Design',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
        };
    }
}
