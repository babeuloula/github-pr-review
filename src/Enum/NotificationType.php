<?php

/**
 * @author BaBeuloula <info@babeuloula.fr>
 */

declare(strict_types=1);

namespace App\Enum;

use MyCLabs\Enum\Enum;

/**
 * @method static NotificationType COMMIT()
 * @method static NotificationType ISSUE()
 * @method static NotificationType PULL_REQUEST()
 * @method static NotificationType RELEASE()
 * @method static NotificationType REPOSITORY_VULNERABILITY_ALERT()
 */
class NotificationType extends Enum
{
    /** @var string */
    protected const COMMIT = 'Commit';

    /** @var string */
    protected const ISSUE = 'Issue';

    /** @var string */
    protected const PULL_REQUEST = 'PullRequest';

    /** @var string */
    protected const RELEASE = 'Release';

    /** @var string  */
    protected const REPOSITORY_VULNERABILITY_ALERT = 'RepositoryVulnerabilityAlert';
}
