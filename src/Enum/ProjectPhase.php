<?php

namespace App\Enum;

enum ProjectPhase: int
{
    case DISCOVERY = 1;
    case WIREFRAMING = 2;
    case DESIGN = 3;
    case PROTOTYPING = 4;
    case DELIVERED = 5;

    public function label(): string
    {
        return match ($this) {
            self::DISCOVERY => 'Discovery & Research',
            self::WIREFRAMING => 'Wireframing',
            self::DESIGN => 'UI/UX Design',
            self::PROTOTYPING => 'Prototyping',
            self::DELIVERED => 'Delivered',
        };
    }

    public function progress(): int
    {
        return match ($this) {
            self::DISCOVERY => 20,
            self::WIREFRAMING => 40,
            self::DESIGN => 60,
            self::PROTOTYPING => 80,
            self::DELIVERED => 100,
        };
    }

    public function status(): string
    {
        return match ($this) {
            self::DISCOVERY => 'Discovery',
            self::WIREFRAMING => 'Wireframing',
            self::DESIGN => 'Design',
            self::PROTOTYPING => 'Prototyping',
            self::DELIVERED => 'Delivered',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::DISCOVERY => 'Stakeholder alignment, UX audits, and user journey discovery.',
            self::WIREFRAMING => 'Information architecture, low-fidelity wireframes, and UX structural flows.',
            self::DESIGN => 'High-fidelity visual design systems, tokens, and responsive UI components.',
            self::PROTOTYPING => 'Clickable prototypes, interactive micro-animations, and usability testing.',
            self::DELIVERED => 'Final design specifications, Figma design token systems, and asset packages.',
        };
    }
}
