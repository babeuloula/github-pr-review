<?php

declare(strict_types=1);

namespace App\Security\Authenticator;

use App\Entity\User;
use App\Repository\UserRepository;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use League\OAuth2\Client\Provider\GithubResourceOwner;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

final class GithubAuthenticator extends OAuth2Authenticator implements AuthenticationEntryPointInterface
{
    public function __construct(
        private readonly ClientRegistry $clientRegistry,
        private readonly RouterInterface $router,
        private readonly UserRepository $userRepository,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function supports(Request $request): ?bool
    {
        return 'oauth_callback_url' === $request->attributes->get('_route');
    }

    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    public function start(Request $request, ?AuthenticationException $authException = null)
    {
        return new RedirectResponse($this->router->generate('oauth_login'));
    }

    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    public function authenticate(Request $request): Passport
    {
        $client = $this->clientRegistry->getClient('github');
        $accessToken = $this->fetchAccessToken($client);

        return new SelfValidatingPassport(
            new UserBadge(
                $accessToken->getToken(),
                function () use ($accessToken, $client): User {
                    /** @var GithubResourceOwner $githubUser */
                    $githubUser = $client->fetchUserFromToken($accessToken);

                    // 1) have they logged in with Github before? Easy!
                    $existingUser = $this
                        ->userRepository
                        ->findOneBy(['username' => $githubUser->getNickname()])
                    ;

                    if ($existingUser instanceof User) {
                        return $existingUser;
                    }

                    // 2) Maybe you just want to "register" them by creating a User object
                    $user = (new User())
                        ->setName((string) $githubUser->getName())
                        ->setToken($accessToken->getToken())
                        ->setUsername((string) $githubUser->getNickname())
                    ;

                    $this->userRepository->add($user, true);

                    return $user;
                }
            )
        );
    }

    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        /** @var Session $session */
        $session = $request->getSession();
        $session->getFlashBag()->add('success', 'Github OAuth connection success.');

        return new RedirectResponse($this->router->generate('home'));
    }

    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $error = strtr($exception->getMessageKey(), $exception->getMessageData());

        /** @var Session $session */
        $session = $request->getSession();
        $session->getFlashBag()->add('error', $error);

        $this->logger->error(
            'Github OAuth connection failed',
            [
                'exception' => $exception,
                'error' => $error,
                'request' => $request->query->all(),
            ]
        );

        return new RedirectResponse($this->router->generate('home'));
    }
}
