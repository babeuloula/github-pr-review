<?php

declare(strict_types=1);

namespace App\Service\Github;

use App\Entity\User;
use App\Service\User\UserService;
use Github\AuthMethod;
use Github\Client;

final class GithubClientService
{
    private Client $client;

    public function __construct(UserService $userService)
    {
        $this->client = new Client();

        if (false === $userService->getUser() instanceof User) {
            return;
        }

        $this->client->authenticate($userService->getUser()->getToken(), null, AuthMethod::ACCESS_TOKEN);
    }

    public function getClient(): Client
    {
        return $this->client;
    }
}
