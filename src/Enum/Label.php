<?php

declare(strict_types=1);

namespace App\Enum;

enum Label: string
{
    case REVIEW_NEEDED = 'Review needed';
    case CHANGES_REQUESTED = 'Changes requested';
    case ACCEPTED = 'Accepted';
    case WIP = 'WIP';
}
