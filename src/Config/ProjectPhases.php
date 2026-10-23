<?php

namespace App\Config;

/**
 * Single source of truth for the 5-phase project lifecycle.
 * Used identically across Admin and Client portals.
 */
final class ProjectPhases
{
    public const PHASES = [
        1 => [
            'number' => 1,
            'name' => 'Discovery & Research',
            'progress' => 20,
            'status' => 'Discovery',
            'desc' => 'Stakeholder alignment, UX audits, and user journey discovery.',
        ],
        2 => [
            'number' => 2,
            'name' => 'Wireframing',
            'progress' => 40,
            'status' => 'Wireframing',
            'desc' => 'Information architecture, low-fidelity wireframes, and UX structural flows.',
        ],
        3 => [
            'number' => 3,
            'name' => 'UI/UX Design',
            'progress' => 60,
            'status' => 'Design',
            'desc' => 'High-fidelity visual design systems, tokens, and responsive UI components.',
        ],
        4 => [
            'number' => 4,
            'name' => 'Prototyping',
            'progress' => 80,
            'status' => 'Prototyping',
            'desc' => 'Clickable prototypes, interactive micro-animations, and usability testing.',
        ],
        5 => [
            'number' => 5,
            'name' => 'Delivered',
            'progress' => 100,
            'status' => 'Delivered',
            'desc' => 'Final design specifications, Figma design token systems, and asset packages.',
        ],
    ];

    /**
     * @return array<int, array{number: int, name: string, progress: int, status: string, desc: string}>
     */
    public static function getAll(): array
    {
        return self::PHASES;
    }

    /**
     * @return array{number: int, name: string, progress: int, status: string, desc: string}
     */
    public static function getPhase(int $phaseNumber): array
    {
        return self::PHASES[$phaseNumber] ?? self::PHASES[1];
    }
}
