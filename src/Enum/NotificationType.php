<?php

declare(strict_types=1);

namespace App\Enum;

enum NotificationType: string
{
    case COMMIT = 'Commit';
    case ISSUE = 'Issue';
    case PULL_REQUEST = 'PullRequest';
    case RELEASE = 'Release';
    case REPOSITORY_VULNERABILITY_ALERT = 'RepositoryVulnerabilityAlert';
}
