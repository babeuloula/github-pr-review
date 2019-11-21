<?php

/**
 * @author BaBeuloula <info@babeuloula.fr>
 */

declare(strict_types=1);

namespace App\Factory;

use App\Entity\User;
use League\OAuth2\Client\Provider\GithubResourceOwner;

class UserFactory
{
    public function createFromGithubUser(GithubResourceOwner $githubUser, ?User $existingUser): User
    {
        $user = new User();

        if ($existingUser instanceof User) {
            $user = $existingUser;
        }

        return $user
            ->setEnabled(true)
            ->setName((string) $githubUser->getName())
            ->setNickname((string) $githubUser->getNickname())
        ;
    }
}
