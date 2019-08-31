<?php
/**
 * @author BaBeuloula <info@babeuloula.fr>
 */
declare(strict_types=1);

namespace App\Service\Github;

use App\Enum\Label;
use Github\Api\PullRequest as PullRequestApi;
use Github\Client;
use http\Exception\RuntimeException;

class PullRequestService
{
    /** @var Client */
    protected $client;

    /** @var string[] */
    protected $githubRepos;

    /** @var string[] */
    protected $labelsChangesRequested;

    /** @var string[] */
    protected $labelsAccepted;

    /** @var string[] */
    protected $labelsWip;

    /**
     * @param string[] $githubRepos
     * @param string[] $githubLabelsChangedRequested
     * @param string[] $githubLabelsAccepted
     * @param string[] $githubLabelsWip
     */
    public function __construct(
        string $githubAuthMethod,
        string $githubUsername,
        string $githubPassword,
        string $githubToken,
        array $githubRepos,
        array $githubLabelsChangedRequested,
        array $githubLabelsAccepted,
        array $githubLabelsWip
    ) {
        $this->client = new Client();

        if (Client::AUTH_HTTP_TOKEN === $githubAuthMethod) {
            $this->client->authenticate($githubToken, null, Client::AUTH_HTTP_TOKEN);
        } elseif(Client::AUTH_HTTP_PASSWORD === $githubAuthMethod) {
            $this->client->authenticate($githubUsername, $githubPassword, Client::AUTH_HTTP_PASSWORD);
        } else {
            throw new RuntimeException("Auth method '$githubAuthMethod' is not implemented yet.");
        }

        $this->githubRepos = $githubRepos;
        natcasesort($this->githubRepos);

        $this->labelsChangesRequested = $githubLabelsChangedRequested;
        $this->labelsAccepted = $githubLabelsAccepted;
        $this->labelsWip = $githubLabelsWip;
    }

    public function getOpen(): array
    {
        return $this->search([
            'sort' => 'updated',
            'direction' => 'desc'
        ]);
    }

    protected function search(array $params = []): array
    {
        $pullRequests = [];

        foreach ($this->githubRepos as $githubRepo) {
            [$username, $repository] = explode("/", $githubRepo);

            $pullRequests[$githubRepo] = $this->sortByLabel(
                $this->getAll($username, $repository, $params)
            );
        }

        return $pullRequests;
    }

    protected function getAll(string $username, string $repository, array $params): array
    {
        /** @var PullRequestApi $pullRequestApi */
        $pullRequestApi = $this->client->api('pullRequest');

        $pullRequest = $pullRequestApi->all($username, $repository, $params);

        if (\count($pullRequest) === 30) {
            $pullRequest = array_merge(
                $this->getAll(
                    $username,
                    $repository,
                    array_merge($params, ['page' => ($params['page'] ?? 1) + 1])
                ),
                $pullRequest
            );
        }

        return $pullRequest;
    }

    protected function sortByLabel(array $pullRequests): array
    {
        $pullRequestsSorted = [
            Label::REVIEW_NEEDED()->getValue() => [],
            Label::ACCEPTED()->getValue() => [],
            Label::CHANGES_REQUESTED()->getValue() => [],
            Label::WIP()->getValue() => [],
        ];

        foreach ($pullRequests as $key => $pullRequest) {
            $labelEnum = Label::REVIEW_NEEDED();

            foreach ($pullRequest['labels'] as $label) {
                if (in_array($label['name'], $this->labelsChangesRequested, true)) {
                    $labelEnum = Label::CHANGES_REQUESTED();

                    break;
                } elseif (in_array($label['name'], $this->labelsAccepted, true)) {
                    $labelEnum = Label::ACCEPTED();

                    break;
                } elseif (in_array($label['name'], $this->labelsWip, true)) {
                    $labelEnum = Label::WIP();

                    break;
                }
            }

            $pullRequestsSorted[$labelEnum->getValue()][] = $pullRequest;
        }

        return $pullRequestsSorted;
    }
}
