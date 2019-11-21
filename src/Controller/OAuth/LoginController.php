<?php

/**
 * @author BaBeuloula <info@babeuloula.fr>
 */

declare(strict_types=1);

namespace App\Controller\OAuth;

use App\Security\GithubGuard;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class LoginController
{
    /** @var ClientRegistry */
    private $clientRegistry;

    public function __construct(ClientRegistry $clientRegistry)
    {
        $this->clientRegistry = $clientRegistry;
    }

    public function __invoke(): RedirectResponse
    {
        return $this
            ->clientRegistry
            ->getClient('github')
            ->redirect(
                GithubGuard::SCOPES,
                []
            )
        ;
    }
}
