<?php

/**
 * @author BaBeuloula <info@babeuloula.fr>
 */

declare(strict_types=1);

namespace App\Enum;

use MyCLabs\Enum\Enum;

/**
 * @method static Label REVIEW_NEEDED()
 * @method static Label CHANGES_REQUESTED()
 * @method static Label ACCEPTED()
 * @method static Label WIP()
 */
class Label extends Enum
{
    /** @var string */
    protected const REVIEW_NEEDED = 'Review needed';

    /** @var string */
    protected const CHANGES_REQUESTED = 'Changes requested';

    /** @var string */
    protected const ACCEPTED = 'Accepted';

    /** @var string */
    protected const WIP = 'WIP';
}
