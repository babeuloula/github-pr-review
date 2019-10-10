<?php

/**
 * @author BaBeuloula <info@babeuloula.fr>
 */

declare(strict_types=1);

namespace App\Service\Github;

interface PullRequestServiceInterface
{
    public function getOpen(): array;

    public function getOpenCount(): array;
}
