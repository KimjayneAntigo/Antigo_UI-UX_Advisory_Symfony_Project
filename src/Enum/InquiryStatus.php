<?php

namespace App\Enum;

enum InquiryStatus: string
{
    case NEW = 'new';
    case REVIEWED = 'reviewed';
    case CONTACTED = 'contacted';
    case CONVERTED = 'converted';
    case LOST = 'lost';

    public function label(): string
    {
        return match ($this) {
            self::NEW => 'New',
            self::REVIEWED => 'Reviewed',
            self::CONTACTED => 'Contacted',
            self::CONVERTED => 'Converted',
            self::LOST => 'Lost',
        };
    }
}
