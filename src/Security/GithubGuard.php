<?php

/**
 * @author BaBeuloula <info@babeuloula.fr>
 */

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use App\Factory\UserFactory;
use App\Repository\UserRepository;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use League\OAuth2\Client\Provider\GithubResourceOwner;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class GithubGuard extends SocialAuthenticator
{
    public const SCOPES = ['repo', 'read:org'];

    /** @var ClientRegistry */
    protected $clientRegistry;

    /** @var UrlGeneratorInterface */
    protected $router;

    /** @var UserRepository */
    protected $userRepository;

    /** @var UserFactory */
    protected $userFactory;

    /** @var FlashBagInterface */
    protected $flashBag;

    /** @var LoggerInterface */
    protected $logger;

    public function __construct(
        ClientRegistry $clientRegistry,
        UrlGeneratorInterface $router,
        UserRepository $userRepository,
        UserFactory $userFactory,
        FlashBagInterface $flashBag,
        LoggerInterface $logger
    ) {
        $this->clientRegistry = $clientRegistry;
        $this->router = $router;
        $this->userRepository = $userRepository;
        $this->userFactory = $userFactory;
        $this->flashBag = $flashBag;
        $this->logger = $logger;
    }

    // phpcs:ignore
    public function start(Request $request, AuthenticationException $authException = null): RedirectResponse
    {
        return new RedirectResponse(
            $this->router->generate('oauth_login')
        );
    }

    public function supports(Request $request): bool
    {
        return 'oauth_callback_url' === $request->attributes->get('_route');
    }

    // phpcs:ignore
    public function getCredentials(Request $request): AccessToken
    {
        return $this->fetchAccessToken(
            $this->clientRegistry->getClient('github')
        );
    }

    /** @param AccessToken $credentials */
    // phpcs:ignore
    public function getUser($credentials, UserProviderInterface $userProvider): UserInterface
    {
        /** @var GithubResourceOwner $githubUser */
        $githubUser = $this->clientRegistry->getClient('github')->fetchUserFromToken($credentials);
        $user = $this->userRepository->findByNickname($githubUser->getNickname());

        if ($user instanceof User && $credentials->getToken() === $user->getToken()) {
            return $user;
        }

        return $this->userRepository->save(
            $this
                ->userFactory
                ->createFromGithubUser($githubUser, $user)
                ->setToken($credentials->getToken())
        );
    }

    // phpcs:ignore
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): RedirectResponse
    {
        $this->flashBag->add('error', 'Github OAuth connection failed.');
        $this->logger->error(
            'Github OAuth connection failed',
            [
                'exception' => $exception,
                'request' => $request->query->all(),
            ]
        );

        return new RedirectResponse($this->router->generate('home'));
    }

    // phpcs:ignore
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): RedirectResponse
    {
        $this->flashBag->add('success', 'Github OAuth connection success.');

        return new RedirectResponse($this->router->generate('home'));
    }
}
