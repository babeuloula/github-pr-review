<?php

/**
 * @author BaBeuloula <info@babeuloula.fr>
 */

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Configuration;
use App\Enum\Color;

class ConfigurationFactory
{
    public const DEFAULT_RELOAD_EVERY = 60;

    public function createDefault(): Configuration
    {
        return (new Configuration())
            ->setMode('label')
            ->setBranchDefaultColor((string) Color::PRIMARY())
            ->setEnabledDarkTheme(false)
            ->setReloadOnFocus(false)
            ->setReloadEvery(static::DEFAULT_RELOAD_EVERY)
        ;
    }
}
