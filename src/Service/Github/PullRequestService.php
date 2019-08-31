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
    /** @var string[] */
    protected const LABELS_CHANGES_REQUESTED = ['Changes requested', 'Modifications demandées'];

    /** @var string[] */
    protected const LABELS_ACCEPTED = ['Accepted', 'Accepté'];

    /** @var string[] */
    protected const LABELS_WIP = ['WIP'];

    /** @var Client */
    protected $client;

    /** @var array */
    protected $githubRepos;

    public function __construct(
        string $githubAuthMethod,
        string $githubUsername,
        string $githubPassword,
        string $githubToken,
        string $githubRepos
    ) {
        $this->client = new Client();

        if (Client::AUTH_HTTP_TOKEN === $githubAuthMethod) {
            $this->client->authenticate($githubToken, null, Client::AUTH_HTTP_TOKEN);
        } elseif(Client::AUTH_HTTP_PASSWORD === $githubAuthMethod) {
            $this->client->authenticate($githubUsername, $githubPassword, Client::AUTH_HTTP_PASSWORD);
        } else {
            throw new RuntimeException("Auth method '$githubAuthMethod' is not implemented yet.");
        }

        $this->githubRepos = explode('|', $githubRepos);
        natcasesort($this->githubRepos);
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
        $pr = [];

        foreach ($this->githubRepos as $githubRepo) {
            $repo = explode("/", $githubRepo);

            $pullRequests = $this->getAll($repo[0], $repo[1], $params);
            $pullRequests = $this->sortByLabel($pullRequests);

            $pr[$githubRepo] = $pullRequests;
        }

        return $pr;
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
                if (in_array($label['name'], static::LABELS_CHANGES_REQUESTED, true)) {
                    $labelEnum = Label::CHANGES_REQUESTED();

                    break;
                } elseif (in_array($label['name'], static::LABELS_ACCEPTED, true)) {
                    $labelEnum = Label::ACCEPTED();

                    break;
                } elseif (in_array($label['name'], static::LABELS_WIP, true)) {
                    $labelEnum = Label::WIP();

                    break;
                }
            }

            $pullRequestsSorted[$labelEnum->getValue()][] = $pullRequest;
        }

        return $pullRequestsSorted;
    }
}
