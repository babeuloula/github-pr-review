<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class DeleteController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UrlGeneratorInterface $router,
        private readonly TokenStorageInterface $tokenStorage
    ) {
    }

    /** @param User $user */
    #[Route('/user/delete', name: 'user_delete_account', methods: Request::METHOD_GET)]
    public function __invoke(Request $request, UserInterface $user): RedirectResponse
    {
        $this->userRepository->remove($user, true);
        $this->tokenStorage->setToken();

        /** @var Session $session */
        $session = $request->getSession();
        $session->getFlashBag()->add('success', 'Your account was deleted with success.');

        return new RedirectResponse(
            $this->router->generate('home')
        );
    }
}
