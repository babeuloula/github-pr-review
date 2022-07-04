<?php

declare(strict_types=1);

namespace App\Enum;

enum NotificationReason: string
{
    // You were assigned to the issue.
    case ASSIGN = 'assign';

    // You created the thread.
    case AUTHOR = 'author';

    // You commented on the thread.
    case COMMENT = 'comment';

    // You accepted an invitation to contribute to the repository.
    case INVITATION = 'invitation';

    // You subscribed to the thread (via an issue or pull request).
    case MANUAL = 'manual';

    // You were specifically @mentioned in the content.
    case MENTION = 'mention';

    // You, or a team you're a member of, were requested to review a pull request
    case REVIEW_REQUESTED = 'review_requested';

    // GitHub discovered a security vulnerability in your repository.
    case SECURITY_ALERT = 'security_alert';

    // You changed the thread state (for example, closing an issue or merging a pull request).
    case STATE_CHANGE = 'state_change';

    // You're watching the repository.
    case SUBSCRIBED = 'subscribed';

    // You were on a team that was mentioned.
    case TEAM_MENTION = 'team_mention';

    public function getName(): string
    {
        return \ucfirst(\str_replace('_', ' ', $this->value));
    }
}
