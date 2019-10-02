<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @license     Proprietary
 * @copyright   Copyright (c) Wizacha
 */
declare(strict_types=1);

namespace App\Traits;

use App\TypedArray\Type\PullRequest;

trait PullRequestTypedArrayTrait
{
    /** @param mixed[] $pullRequest */
    public function convertToTypedArray(array $pullRequest): PullRequest
    {
        $pullRequest = (new PullRequest($pullRequest))->setBranchColor($this->branchDefaultColor);

        /** @var array $branchColor */
        foreach ($this->branchsColors as $branchColor) {
            $branch = \array_keys($branchColor)[0];
            $color = \array_values($branchColor)[0];

            if (\is_string($pullRequest->getBase())
                && \preg_match("/".$branch."/", $pullRequest->getBase()) === 1
            ) {
                $pullRequest->setBranchColor($color);
                break;
            }
        }

        return $pullRequest;
    }

    /** @return string[] */
    abstract protected function getBranchsColors(): array;
}
