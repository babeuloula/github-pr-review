<?php

declare(strict_types=1);

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class OAuthController extends AbstractController
{
    public function __construct(readonly private ClientRegistry $clientRegistry)
    {
    }

    #[Route('/oauth/login', name: 'oauth_login', methods: Request::METHOD_GET)]
    public function login(): RedirectResponse
    {
        return $this
            ->clientRegistry
            ->getClient('github')
            ->redirect(
                ['repo', 'read:org'],
                []
            )
        ;
    }

    #[Route('/oauth/callback-url', name: 'oauth_callback_url', methods: Request::METHOD_GET)]
    public function callbackUrl(): void
    {
    }

    #[Route('/oauth/logout', name: 'oauth_logout', methods: Request::METHOD_GET)]
    public function logout(): void
    {
    }
}
