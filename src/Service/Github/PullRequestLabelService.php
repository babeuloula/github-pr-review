<?php

declare(strict_types=1);

namespace App\Service\Github;

use App\Contract\Service\PullRequestServiceInterface;
use App\Entity\User;
use App\Enum\Label;
use App\Model\AbstractPullRequestService;
use App\Service\User\UserService;
use Github\Client;
use Github\ResultPager;

final class PullRequestLabelService extends AbstractPullRequestService implements PullRequestServiceInterface
{
    private Client $client;

    /** @var string[] */
    private array $githubRepos;

    /** @var string[] */
    private array $labelsReviewNeeded;

    /** @var string[] */
    private array $labelsChangesRequested;

    /** @var string[] */
    private array $labelsAccepted;

    /** @var string[] */
    private array $labelsWip;

    /** @var array[] */
    private array $openCount = [];

    public function __construct(GithubClientService $client, UserService $userService)
    {
        if (false === $userService->getUser() instanceof User) {
            return;
        }

        $this->client = $client->getClient();
        $this->githubRepos = $userService->getUser()->getConfiguration()->getRepositories();
        \natcasesort($this->githubRepos);

        $this->labelsReviewNeeded = $userService->getUser()->getConfiguration()->getLabelsReviewNeeded();
        $this->labelsChangesRequested = $userService->getUser()->getConfiguration()->getLabelsChangesRequested();
        $this->labelsAccepted = $userService->getUser()->getConfiguration()->getLabelsAccepted();
        $this->labelsWip = $userService->getUser()->getConfiguration()->getLabelsWip();
        $this->branchesColors = array_map(
            // phpcs:ignore
            static function (array $data): array { // @phpstan-ignore-line
                return [$data[0] => $data[1]];
            },
            $userService->getUser()->getConfiguration()->getBranchesColors(),
        );
        $this->branchDefaultColor = $userService->getUser()->getConfiguration()->getBranchDefaultColor()->value;

        $this->openCount = [
            Label::REVIEW_NEEDED->value => [],
            Label::ACCEPTED->value => [],
            Label::CHANGES_REQUESTED->value => [],
            Label::WIP->value => [],
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
                $this->openCount[$label][$githubRepo] = \count($pullRequestArray);
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
        return (new ResultPager($this->client))
            ->fetchAll(
                $this->client->api('pullRequest'),
                'all',
                [$username, $repository, $params]
            )
        ;
    }

    /**
     * @param array[] $pullRequests
     *
     * @return array[]
     */
    private function sortByLabel(array $pullRequests): array
    {
        $pullRequestsSorted = [
            Label::REVIEW_NEEDED->value => [],
            Label::ACCEPTED->value => [],
            Label::CHANGES_REQUESTED->value => [],
            Label::WIP->value => [],
        ];

        foreach ($pullRequests as $pullRequest) {
            $labelEnum = Label::REVIEW_NEEDED;

            foreach ($pullRequest['labels'] as $label) {
                if (true === \in_array($label['name'], $this->labelsWip, true)) {
                    $labelEnum = Label::WIP;

                    break;
                } elseif (true === \in_array($label['name'], $this->labelsAccepted, true)) {
                    $labelEnum = Label::ACCEPTED;

                    break;
                } elseif (true === \in_array($label['name'], $this->labelsReviewNeeded, true)) {
                    $labelEnum = Label::REVIEW_NEEDED;

                    break;
                } elseif (true === \in_array($label['name'], $this->labelsChangesRequested, true)) {
                    $labelEnum = Label::CHANGES_REQUESTED;

                    break;
                }
            }

            $pullRequestsSorted[$labelEnum->value][] = $this->convertToTypedArray($pullRequest);
        }

        return $pullRequestsSorted;
    }

    /** @return array[] */
    protected function getBranchesColors(): array
    {
        return $this->branchesColors;
    }
}
