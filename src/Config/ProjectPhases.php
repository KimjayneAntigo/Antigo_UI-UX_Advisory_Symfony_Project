<?php

namespace App\Config;

use App\Enum\ProjectPhase;

/**
 * Adapter that exposes the backed enum ProjectPhase to Twig / templates.
 * ProjectPhase is the single canonical definition for the 5-phase project lifecycle.
 */
final class ProjectPhases
{
    /**
     * @return array<int, array{number: int, name: string, progress: int, status: string, desc: string}>
     */
    public static function getAll(): array
    {
        $phases = [];
        foreach (ProjectPhase::cases() as $phase) {
            $phases[$phase->value] = [
                'number' => $phase->value,
                'name' => $phase->label(),
                'progress' => $phase->progress(),
                'status' => $phase->status(),
                'desc' => $phase->description(),
            ];
        }

        return $phases;
    }

    /**
     * @return array{number: int, name: string, progress: int, status: string, desc: string}
     */
    public static function getPhase(int $phaseNumber): array
    {
        $phase = ProjectPhase::tryFrom($phaseNumber) ?? ProjectPhase::DISCOVERY;

        return [
            'number' => $phase->value,
            'name' => $phase->label(),
            'progress' => $phase->progress(),
            'status' => $phase->status(),
            'desc' => $phase->description(),
        ];
    }
}
