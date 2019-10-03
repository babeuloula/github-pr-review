<?php
/**
 * @author BaBeuloula <info@babeuloula.fr>
 */

declare(strict_types=1);

namespace App\Service\Github;

use Github\Client;

class GithubClientService
{
    /** @var Client */
    protected $client;

    public function __construct(
        string $githubAuthMethod,
        string $githubUsername,
        string $githubPassword,
        string $githubToken
    ) {
        $this->client = new Client();

        if (Client::AUTH_HTTP_TOKEN === $githubAuthMethod) {
            $this->client->authenticate($githubToken, null, Client::AUTH_HTTP_TOKEN);
        } elseif (Client::AUTH_HTTP_PASSWORD === $githubAuthMethod) {
            $this->client->authenticate($githubUsername, $githubPassword, Client::AUTH_HTTP_PASSWORD);
        } else {
            throw new \RuntimeException("Auth method '$githubAuthMethod' is not implemented yet.");
        }
    }

    public function getClient(): Client
    {
        return $this->client;
    }
}
