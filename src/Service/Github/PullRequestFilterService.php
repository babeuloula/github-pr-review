<?php
/**
 * @author BaBeuloula <info@babeuloula.fr>
 */

declare(strict_types=1);

namespace App\Service\Github;

use App\Enum\UseMode;
use App\Traits\PullRequestTypedArrayTrait;
use App\TypedArray\PullRequestArray;
use Github\Api\PullRequest as PullRequestApi;
use Github\Api\Search;
use Github\Client;

class PullRequestFilterService implements PullRequestServiceInterface
{
    use PullRequestTypedArrayTrait;

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
        string $useMode
    ) {
        $this->client = $client->getClient();

        if ((new UseMode($useMode))->equals(UseMode::FILTER()) && 0 === \count($githubFilters)) {
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
                foreach ($this->getAll($username, $repository, $filter, $params) as $pullRequest) {
                    $pullRequest = $pullRequestApi->show($username, $repository, $pullRequest['number']);

                    if (\is_array($pullRequest)) {
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
        $filter .= " repo:$username/$repository";

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

    /** @return string[] */
    protected function getBranchsColors(): array
    {
        return $this->branchsColors;
    }
}
