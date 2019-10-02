<?php
/**
 * @author BaBeuloula <info@babeuloula.fr>
 */

declare(strict_types=1);

namespace App\Service\Github;

use App\TypedArray\PullRequestArray;
use App\TypedArray\Type\PullRequest;
use Github\Api\Search;
use Github\Client;

class PullRequestFilterService implements PullRequestServiceInterface
{
    protected const OTHER_REPOS = 'Other repos';

    /** @var Client */
    protected $client;

    /** @var string[] */
    protected $githubRepos;

    /** @var string[] */
    protected $branchsColors;

    /** @var string */
    protected $branchDefaultColor;

    /** @var string[] */
    protected $githubFilters;

    /**
     * @param string[] $githubRepos
     * @param string[] $githubBranchsColors
     * @param string[] $githubFilters
     */
    public function __construct(
        GithubClientService $client,
        array $githubRepos,
        array $githubBranchsColors,
        string $githubBranchDefaultColor,
        array $githubFilters
    ) {
        $this->client = $client->getClient();

        if (0 === \count($githubFilters)) {
            throw new \RuntimeException("Option Github Filters cannot be empty.");
        }

        $this->githubRepos = $githubRepos;
        \natcasesort($this->githubRepos);

        $this->branchsColors = $githubBranchsColors;
        $this->branchDefaultColor = $githubBranchDefaultColor;
        $this->githubFilters = $githubFilters;
    }

    public function getOpen(): array
    {
        return $this->search([
            'sort' => 'updated',
            'direction' => 'desc',
        ]);
    }

    /**
     * @param mixed[] $params
     *
     * @return PullRequestArray[]
     */
    protected function search(array $params = []): array
    {
        $pullRequests = [];

        foreach ($this->githubRepos as $githubRepo) {
            [$username, $repository] = \explode("/", $githubRepo);
            $pullRequestsArray = new PullRequestArray();

            foreach ($this->getAll($username, $repository, $params) as $pullRequest) {
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

                $pullRequestsArray[$pullRequest->getUrl()] = $pullRequest;
            }

            $pullRequests[$githubRepo] = $pullRequestsArray;
        }

        return $pullRequests;
    }

    /** @return array[] */
    protected function getAll(string $username, string $repository, array $params): array
    {
        $pullRequests = [];
        /** @var Search $searchApi */
        $searchApi = $this->client->api('search');

        foreach ($this->githubFilters as $filter) {
            $filter .= " repo:$username/$repository";

            // Issues and PRs use the same endpoint
            $pullRequests = $searchApi->issues($filter)['items'];

            if (\count($pullRequests) === 30) {
                $pullRequests = \array_merge(
                    $this->getAll(
                        $username,
                        $repository,
                        \array_merge($params, ['page' => ($params['page'] ?? 1) + 1])
                    ),
                    $pullRequests
                );
            }
        }

        return $pullRequests;
    }
}
