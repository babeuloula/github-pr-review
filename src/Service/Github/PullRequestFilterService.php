<?php
/**
 * @author BaBeuloula <info@babeuloula.fr>
 */

declare(strict_types=1);

namespace App\Service\Github;

use App\Traits\PullRequestTypedArrayTrait;
use App\TypedArray\PullRequestArray;
use Github\Api\PullRequest as PullRequestApi;
use Github\Api\Search;
use Github\Client;

class PullRequestFilterService implements PullRequestServiceInterface
{
    use PullRequestTypedArrayTrait;

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

    /** @var int[] */
    protected $openCount = [];

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
        array $githubFilters,
        bool $useFilters
    ) {
        $this->client = $client->getClient();

        if (true === $useFilters && 0 === \count($githubFilters)) {
            throw new \RuntimeException("Option Github Filters cannot be empty.");
        }

        $this->githubRepos = $githubRepos;
        \natcasesort($this->githubRepos);

        $this->branchsColors = $githubBranchsColors;
        $this->branchDefaultColor = $githubBranchDefaultColor;
        $this->githubFilters = $githubFilters;
    }

    /** @return PullRequestArray[] */
    public function getOpen(): array
    {
        return $this->search([
            'sort' => 'updated',
            'direction' => 'desc',
        ]);
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

            foreach ($this->getAll($username, $repository, $params) as $pullRequest) {
                $pullRequest = $this->convertToTypedArray(
                    $pullRequestApi->show($username, $repository, $pullRequest['number'])
                );
                $pullRequestsArray[$pullRequest->getUrl()] = $pullRequest;
            }

            $this->openCount[$githubRepo] = $pullRequestsArray->count();
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

    /** @return string[] */
    protected function getBranchsColors(): array
    {
        return $this->branchsColors;
    }
}
