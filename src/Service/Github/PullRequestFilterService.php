<?php

/**
 * @author BaBeuloula <info@babeuloula.fr>
 */

declare(strict_types=1);

namespace App\Service\Github;

use App\Entity\Configuration;
use App\Entity\User;
use App\Service\User\UserService;
use App\Traits\PullRequestTypedArrayTrait;
use App\TypedArray\PullRequestArray;
use Github\Api\PullRequest as PullRequestApi;
use Github\Api\Search;
use Github\Client;

class PullRequestFilterService implements PullRequestServiceInterface
{
    use PullRequestTypedArrayTrait;

    protected Client $client;

    /** @var string[] */
    protected array $githubRepos;

    /** @var array[] */
    protected array $branchsColors;

    protected string $branchDefaultColor;

    /** @var string[] */
    protected array $githubFilters;

    /** @var int[] */
    protected array $openCount = [];

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
        $this->branchDefaultColor = (string) $userService->getUser()->getConfiguration()->getBranchDefaultColor();
        $this->githubFilters = $userService->getUser()->getConfiguration()->getFilters();
    }

    /** @return PullRequestArray[] */
    public function getOpen(): array
    {
        return $this->search();
    }

    /** return int[] */
    public function getOpenCount(): array
    {
        return $this->openCount;
    }

    /**
     * @param mixed[] $params
     *
     * @return PullRequestArray[]
     */
    protected function search(array $params = []): array
    {
        /** @var PullRequestApi $pullRequestApi */
        $pullRequestApi = $this->client->api('pullRequest');
        $pullRequests = [];

        foreach ($this->githubRepos as $githubRepo) {
            [$username, $repository] = \explode("/", $githubRepo);
            $pullRequestsArray = new PullRequestArray();

            // Filters example:
            //   - "is:pr is:open -label:WIP"
            //   - "is:pr is:draft"
            foreach ($this->githubFilters as $filter) {
                if (1 === \preg_match("/repo\:[a-zA-Z0-9\/-]+/", $filter, $matches)
                    && "repo:$username/$repository" !== $matches[0]
                ) {
                    continue;
                }

                foreach ($this->getAll($username, $repository, $filter, $params) as $pullRequest) {
                    $pullRequest = $pullRequestApi->show($username, $repository, $pullRequest['number']);

                    if (true === \is_array($pullRequest)) {
                        $pullRequest = $this->convertToTypedArray($pullRequest, true);
                        $pullRequestsArray[$pullRequest->getUrl()] = $pullRequest;
                    }
                }
            }

            $this->openCount[$githubRepo] = $pullRequestsArray->count();
            $pullRequests[$githubRepo] = $pullRequestsArray;
        }

        return $pullRequests;
    }

    /** @return array[] */
    protected function getAll(string $username, string $repository, string $filter, array $params): array
    {
        /** @var Search $searchApi */
        $searchApi = $this->client->api('search');

        if (0 === \preg_match("/repo\:[a-zA-Z0-9\/-]+/", $filter)) {
            $filter = "repo:$username/$repository $filter";
        }

        // Issues and PRs use the same method
        $pullRequests = $searchApi->issues($filter)['items'];

        // Github does not offer a system indicating the total number of PRs.
        // We are therefore obliged to detect the number of returns.
        // If we have 30, it's because there's a next page. If we have less, we are on the last page.
        if (\count($pullRequests) === 30) {
            $pullRequests = \array_merge(
                $this->getAll(
                    $username,
                    $repository,
                    $filter,
                    \array_merge($params, ['page' => ($params['page'] ?? 1) + 1])
                ),
                $pullRequests
            );
        }

        return $pullRequests;
    }

    /** @return array[] */
    protected function getBranchsColors(): array
    {
        return $this->branchsColors;
    }
}
