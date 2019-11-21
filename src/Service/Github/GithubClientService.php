<?php

/**
 * @author BaBeuloula <info@babeuloula.fr>
 */

declare(strict_types=1);

namespace App\Service\Github;

use App\Entity\User;
use App\Service\User\UserService;
use Github\Client;

class GithubClientService
{
    /** @var Client */
    protected $client;

    public function __construct(UserService $userService)
    {
        $this->client = new Client();

        if (false === $userService->getUser() instanceof User
            || false === \is_string($userService->getUser()->getToken())
        ) {
            return;
        }

        $this->client->authenticate($userService->getUser()->getToken(), null, Client::AUTH_HTTP_TOKEN);
    }

    public function getClient(): Client
    {
        return $this->client;
    }
}
