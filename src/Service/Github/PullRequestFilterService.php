<?php

declare(strict_types=1);

namespace App\Service\Github;

use App\Contract\Service\PullRequestServiceInterface;
use App\Entity\User;
use App\Model\AbstractPullRequestService;
use App\Service\User\UserService;
use Github\Api\PullRequest as PullRequestApi;
use Github\Client;
use Github\ResultPager;

final class PullRequestFilterService extends AbstractPullRequestService implements PullRequestServiceInterface
{
    private Client $client;

    /** @var string[] */
    private array $githubRepos;

    /** @var string[] */
    private array $githubFilters;

    /** @var int[] */
    private array $openCount = [];

    public function __construct(GithubClientService $client, UserService $userService)
    {
        if (false === $userService->getUser() instanceof User) {
            return;
        }

        $this->client = $client->getClient();

        $this->githubRepos = $userService->getUser()->getConfiguration()->getRepositories();
        \natcasesort($this->githubRepos);

        $this->branchesColors = array_map(
            // phpcs:ignore
            static function (array $data): array { // @phpstan-ignore-line
                return [$data[0] => $data[1]];
            },
            $userService->getUser()->getConfiguration()->getBranchesColors()
        );
        $this->branchDefaultColor = $userService->getUser()->getConfiguration()->getBranchDefaultColor()->value;
        $this->githubFilters = $userService->getUser()->getConfiguration()->getFilters();
    }

    /** @return array[] */
    public function getOpen(): array
    {
        return $this->search();
    }

    /** @return int[] */
    public function getOpenCount(): array
    {
        return $this->openCount;
    }

    /** @return array[] */
    private function search(): array
    {
        /** @var PullRequestApi $pullRequestApi */
        $pullRequestApi = $this->client->api('pullRequest');
        $pullRequests = [];

        foreach ($this->githubRepos as $githubRepo) {
            [$username, $repository] = \explode("/", $githubRepo);
            $pullRequestsArray = [];

            // Filters example:
            //   - "is:pr is:open -label:WIP"
            //   - "is:pr is:draft"
            foreach ($this->githubFilters as $filter) {
                if (1 === \preg_match("/repo\:[a-zA-Z0-9\/-]+/", $filter, $matches)
                    && "repo:$username/$repository" !== $matches[0]
                ) {
                    continue;
                }

                foreach ($this->getAll($username, $repository, $filter) as $pullRequest) {
                    $pullRequest = $pullRequestApi->show($username, $repository, $pullRequest['number']);

                    if (true === \is_array($pullRequest)) {
                        $pullRequest = $this->convertToTypedArray($pullRequest, true);
                        $pullRequestsArray[$pullRequest->getUrl()] = $pullRequest;
                    }
                }
            }

            $this->openCount[$githubRepo] = \count($pullRequestsArray);
            $pullRequests[$githubRepo] = $pullRequestsArray;
        }

        return $pullRequests;
    }

    /** @return array[] */
    private function getAll(string $username, string $repository, string $filter): array
    {
        if (0 === \preg_match("/repo\:[a-zA-Z0-9\/-]+/", $filter)) {
            $filter = "repo:$username/$repository $filter";
        }

        return (new ResultPager($this->client))
            ->fetchAll(
                $this->client->api('search'),
                'issues',
                [$filter]
            )
        ;
    }

    /** @return array[] */
    protected function getBranchesColors(): array
    {
        return $this->branchesColors;
    }
}
