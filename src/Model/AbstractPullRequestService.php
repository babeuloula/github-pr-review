<?php

declare(strict_types=1);

namespace App\Model;

use App\Dto\PullRequest;

abstract class AbstractPullRequestService
{
    /** @var array[] */
    protected array $branchesColors;
    protected string $branchDefaultColor;

    /** @param mixed[] $pullRequest */
    public function convertToTypedArray(array $pullRequest, bool $headColor = false): PullRequest
    {
        $pullRequest = (new PullRequest($pullRequest))->setBranchColor($this->branchDefaultColor);

        /** @var array<string, string> $branchColor */
        foreach ($this->branchesColors as $branchColor) {
            $branch = \array_keys($branchColor)[0];
            $color = \array_values($branchColor)[0];

            $branchType = true === $headColor ? $pullRequest->getHead() : $pullRequest->getBase();

            if (true === \is_string($branchType) && 1 === \preg_match("/" . $branch . "/", $branchType)) {
                $pullRequest->setBranchColor($color);
                break;
            }
        }

        return $pullRequest;
    }

    /** @return array[] */
    abstract protected function getBranchesColors(): array;
}
