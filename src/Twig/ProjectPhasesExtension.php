<?php

namespace App\Twig;

use App\Config\ProjectPhases;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFunction;

class ProjectPhasesExtension extends AbstractExtension implements GlobalsInterface
{
    public function getGlobals(): array
    {
        return [
            'project_phases' => ProjectPhases::getAll(),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('project_phase', [ProjectPhases::class, 'getPhase']),
        ];
    }
}
