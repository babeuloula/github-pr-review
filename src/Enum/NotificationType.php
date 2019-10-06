<?php
/**
 * @author BaBeuloula <info@babeuloula.fr>
 */

declare(strict_types=1);

namespace App\Enum;

use MyCLabs\Enum\Enum;

/**
 * @method static NotificationType ISSUE()
 * @method static NotificationType PULL_REQUEST()
 */
class NotificationType extends Enum
{
    /** @var string */
    protected const ISSUE = 'Issue';

    /** @var string */
    protected const PULL_REQUEST = 'PullRequest';

    /** @var string  */
    protected const REPOSITORY_VULNERABILITY_ALERT = 'RepositoryVulnerabilityAlert';
}
