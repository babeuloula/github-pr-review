<?php

/**
 * @author BaBeuloula <info@babeuloula.fr>
 */

declare(strict_types=1);

namespace App\Enum;

use MyCLabs\Enum\Enum;

/**
 * @method static Color PRIMARY()
 * @method static Color SECONDARY()
 * @method static Color SUCCESS()
 * @method static Color DANGER()
 * @method static Color WARNING()
 * @method static Color INFO()
 * @method static Color LIGHT()
 * @method static Color DARK()
 */
class Color extends Enum
{
    protected const PRIMARY = 'primary';
    protected const SECONDARY = 'secondary';
    protected const SUCCESS = 'success';
    protected const DANGER = 'danger';
    protected const WARNING = 'warning';
    protected const INFO = 'info';
    protected const LIGHT = 'light';
    protected const DARK = 'dark';
}
