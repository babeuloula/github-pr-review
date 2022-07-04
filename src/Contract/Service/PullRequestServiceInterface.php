<?php

declare(strict_types=1);

namespace App\Contract\Service;

interface PullRequestServiceInterface
{
    public function getOpen(): array;

    public function getOpenCount(): array;
}
