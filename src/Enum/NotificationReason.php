<?php

/**
 * @author BaBeuloula <info@babeuloula.fr>
 */

declare(strict_types=1);

namespace App\Enum;

use MyCLabs\Enum\Enum;

/**
 * @method static NotificationReason ASSIGN()
 * @method static NotificationReason AUTHOR()
 * @method static NotificationReason COMMENT()
 * @method static NotificationReason INVITATION()
 * @method static NotificationReason MANUAL()
 * @method static NotificationReason MENTION()
 * @method static NotificationReason REVIEW_REQUESTED()
 * @method static NotificationReason SECURITY_ALERT()
 * @method static NotificationReason STATE_CHANGE()
 * @method static NotificationReason SUBSCRIBED()
 * @method static NotificationReason TEAM_MENTION()
 */
class NotificationReason extends Enum
{
    /** @var string You were assigned to the issue. */
    protected const ASSIGN = 'assign';

    /** @var string You created the thread. */
    protected const AUTHOR = 'author';

    /** @var string You commented on the thread. */
    protected const COMMENT = 'comment';

    /** @var string You accepted an invitation to contribute to the repository. */
    protected const INVITATION = 'invitation';

    /** @var string You subscribed to the thread (via an issue or pull request). */
    protected const MANUAL = 'manual';

    /** @var string You were specifically @mentioned in the content. */
    protected const MENTION = 'mention';

    /** @var string You, or a team you're a member of, were requested to review a pull request */
    protected const REVIEW_REQUESTED = 'review_requested';

    /** @var string GitHub discovered a security vulnerability in your repository. */
    protected const SECURITY_ALERT = 'security_alert';

    /** @var string You changed the thread state (for example, closing an issue or merging a pull request). */
    protected const STATE_CHANGE = 'state_change';

    /** @var string You're watching the repository. */
    protected const SUBSCRIBED = 'subscribed';

    /** @var string You were on a team that was mentioned. */
    protected const TEAM_MENTION = 'team_mention';

    public function getName(): string
    {
        return \ucfirst(\str_replace('_', ' ', $this->value));
    }
}
