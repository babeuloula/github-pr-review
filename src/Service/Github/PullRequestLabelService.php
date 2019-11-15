<?php

/**
 * @author BaBeuloula <info@babeuloula.fr>
 */

declare(strict_types=1);

namespace App\Service\Github;

use App\Entity\Configuration;
use App\Entity\User;
use App\Enum\Label;
use App\Service\User\UserService;
use App\Traits\PullRequestTypedArrayTrait;
use App\TypedArray\PullRequestArray;
use Github\Api\PullRequest as PullRequestApi;
use Github\Client;

class PullRequestLabelService implements PullRequestServiceInterface
{
    use PullRequestTypedArrayTrait;

    /** @var Client */
    protected $client;

    /** @var string[] */
    protected $githubRepos;

    /** @var string[] */
    protected $labelsReviewNeeded;

    /** @var string[] */
    protected $labelsChangesRequested;

    /** @var string[] */
    protected $labelsAccepted;

    /** @var string[] */
    protected $labelsWip;

    /** @var array[] */
    protected $branchsColors;

    /** @var string */
    protected $branchDefaultColor;

    /** @var array[] */
    protected $openCount = [];

    public function __construct(GithubClientService $client, UserService $userService)
    {
        if (false === $userService->getUser() instanceof User
            || false === $userService->getUser()->getConfiguration() instanceof Configuration
        ) {
            return;
        }

        $this->client = $client->getClient();
        $this->githubRepos = $userService->getUser()->getConfiguration()->getRepositories();
        \natcasesort($this->githubRepos);

        $this->labelsReviewNeeded = $userService->getUser()->getConfiguration()->getLabelsReviewNeeded();
        $this->labelsChangesRequested = $userService->getUser()->getConfiguration()->getLabelsChangesRequested();
        $this->labelsAccepted = $userService->getUser()->getConfiguration()->getLabelsAccepted();
        $this->labelsWip = $userService->getUser()->getConfiguration()->getLabelsWip();
        $this->branchsColors = array_map(
            /**
             * @param string[] $data
             *
             * @return string[]
             */
            function (array $data): array {
                return [$data[0] => $data[1]];
            },
            $userService->getUser()->getConfiguration()->getBranchsColors()
        );
        $this->branchDefaultColor = $userService->getUser()->getConfiguration()->getBranchDefaultColor();

        $this->openCount = [
            Label::REVIEW_NEEDED()->getValue() => [],
            Label::ACCEPTED()->getValue() => [],
            Label::CHANGES_REQUESTED()->getValue() => [],
            Label::WIP()->getValue() => [],
        ];
    }

    /** @return array[] */
    public function getOpen(): array
    {
        return $this->search(
            [
                'sort' => 'updated',
                'direction' => 'desc',
            ]
        );
    }

    /** return array[] */
    public function getOpenCount(): array
    {
        return $this->openCount;
    }

    /**
     * @param mixed[] $params
     *
     * @return array[]
     */
    protected function search(array $params = []): array
    {
        $pullRequests = [];

        foreach ($this->githubRepos as $githubRepo) {
            [$username, $repository] = \explode("/", $githubRepo);

            $pullRequestsSorted = $this->sortByLabel(
                $this->getAll($username, $repository, $params)
            );

            foreach ($pullRequestsSorted as $label => $pullRequestArray) {
                $this->openCount[$label][$githubRepo] = $pullRequestArray->count();
            }

            $pullRequests[$githubRepo] = $pullRequestsSorted;
        }

        return $pullRequests;
    }

    /**
     * @param mixed[] $params
     *
     * @return array[]
     */
    protected function getAll(string $username, string $repository, array $params): array
    {
        /** @var PullRequestApi $pullRequestApi */
        $pullRequestApi = $this->client->api('pullRequest');
        $pullRequest = $pullRequestApi->all($username, $repository, $params);

        // Github does not offer a system indicating the total number of PRs.
        // We are therefore obliged to detect the number of returns.
        // If we have 30, it's because there's a next page. If we have less, we are on the last page.
        if (\count($pullRequest) === 30) {
            $pullRequest = \array_merge(
                $this->getAll(
                    $username,
                    $repository,
                    \array_merge($params, ['page' => ($params['page'] ?? 1) + 1])
                ),
                $pullRequest
            );
        }

        return $pullRequest;
    }

    /**
     * @param array[] $pullRequests
     *
     * @return PullRequestArray[]
     */
    protected function sortByLabel(array $pullRequests): array
    {
        $pullRequestsSorted = [
            Label::REVIEW_NEEDED()->getValue() => new PullRequestArray(),
            Label::ACCEPTED()->getValue() => new PullRequestArray(),
            Label::CHANGES_REQUESTED()->getValue() => new PullRequestArray(),
            Label::WIP()->getValue() => new PullRequestArray(),
        ];

        foreach ($pullRequests as $key => $pullRequest) {
            $labelEnum = Label::REVIEW_NEEDED();

            foreach ($pullRequest['labels'] as $label) {
                if (true === \in_array($label['name'], $this->labelsWip, true)) {
                    $labelEnum = Label::WIP();

                    break;
                } elseif (true === \in_array($label['name'], $this->labelsAccepted, true)) {
                    $labelEnum = Label::ACCEPTED();

                    break;
                } elseif (true === \in_array($label['name'], $this->labelsReviewNeeded, true)) {
                    $labelEnum = Label::REVIEW_NEEDED();

                    break;
                } elseif (true === \in_array($label['name'], $this->labelsChangesRequested, true)) {
                    $labelEnum = Label::CHANGES_REQUESTED();

                    break;
                }
            }

            $pullRequestsSorted[$labelEnum->getValue()][] = $this->convertToTypedArray($pullRequest);
        }

        return $pullRequestsSorted;
    }

    /** @return array[] */
    protected function getBranchsColors(): array
    {
        return $this->branchsColors;
    }
}
