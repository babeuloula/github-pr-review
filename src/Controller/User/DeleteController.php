<?php

/**
 * @author BaBeuloula <info@babeuloula.fr>
 */

declare(strict_types=1);

namespace App\Controller\User;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class DeleteController
{
    /** @var FlashBagInterface */
    private $flashBag;

    /** @var UserRepository */
    private $userRepository;

    /** @var UrlGeneratorInterface */
    private $router;

    /** @var TokenStorageInterface */
    protected $tokenStorage;

    public function __construct(
        FlashBagInterface $flashBag,
        UserRepository $userRepository,
        UrlGeneratorInterface $router,
        TokenStorageInterface $tokenStorage
    ) {
        $this->flashBag = $flashBag;
        $this->userRepository = $userRepository;
        $this->router = $router;
        $this->tokenStorage = $tokenStorage;
    }

    /** @param User $user */
    public function __invoke(UserInterface $user): RedirectResponse
    {
        $this->userRepository->delete($user);
        $this->tokenStorage->setToken(null);

        $this->flashBag->add('success', 'Your account was deleted with success.');

        return new RedirectResponse(
            $this->router->generate('home')
        );
    }
}
