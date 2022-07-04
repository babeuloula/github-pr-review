<?php

declare(strict_types=1);

namespace App\Service\User;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class UserService
{
    protected ?User $user = null;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $user = null;

        if ($tokenStorage->getToken() instanceof TokenInterface) {
            /** @var User $user */
            $user = $tokenStorage->getToken()->getUser();
        }

        $this->user = $user;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }
}
