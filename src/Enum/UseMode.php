<?php

/**
 * @author BaBeuloula <info@babeuloula.fr>
 */

declare(strict_types=1);

namespace App\Enum;

use MyCLabs\Enum\Enum;

/**
 * @method static UseMode FILTER()
 * @method static UseMode LABEL()
 */
class UseMode extends Enum
{
    /** @var string */
    protected const FILTER = 'filter';

    /** @var string */
    protected const LABEL = 'label';
}
