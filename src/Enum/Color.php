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
    /** @var string */
    protected const PRIMARY = 'primary';

    /** @var string */
    protected const SECONDARY = 'secondary';

    /** @var string */
    protected const SUCCESS = 'success';

    /** @var string */
    protected const DANGER = 'danger';

    /** @var string */
    protected const WARNING = 'warning';

    /** @var string */
    protected const INFO = 'info';

    /** @var string */
    protected const LIGHT = 'light';

    /** @var string */
    protected const DARK = 'dark';

    public static function getDefault(): self
    {
        return static::PRIMARY();
    }
}
