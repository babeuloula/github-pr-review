<?php

/**
 * @author BaBeuloula <info@babeuloula.fr>
 */

declare(strict_types=1);

namespace App\Traits;

use App\TypedArray\Type\PullRequest;

trait PullRequestTypedArrayTrait
{
    /** @param mixed[] $pullRequest */
    public function convertToTypedArray(array $pullRequest, bool $headColor = false): PullRequest
    {
        $pullRequest = (new PullRequest($pullRequest))->setBranchColor($this->branchDefaultColor);

        /** @var array $branchColor */
        foreach ($this->branchsColors as $branchColor) {
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

    /** @return string[] */
    abstract protected function getBranchsColors(): array;
}
