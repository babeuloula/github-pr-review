<?php

/**
 * @author BaBeuloula <info@babeuloula.fr>
 */

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Exception\GithubGuiException;
use App\Exception\XhrException;
use App\Service\User\UserService;
use Github\Exception\ApiLimitExceedException;
use Github\Exception\RuntimeException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ExceptionSubscriber implements EventSubscriberInterface
{
    /** @var FlashBagInterface */
    protected $flashBag;

    /** @var UrlGeneratorInterface */
    protected $router;

    /** @var LoggerInterface */
    protected $logger;

    /** @var UserService */
    protected $userService;

    public function __construct(
        FlashBagInterface $flashBag,
        UrlGeneratorInterface $router,
        LoggerInterface $logger,
        UserService $userService
    ) {
        $this->flashBag = $flashBag;
        $this->router = $router;
        $this->logger = $logger;
        $this->userService = $userService;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => [
                ['githubApiException', 10],
                ['githubGuiException', 10],
                ['xhrException', 10],
            ],
        ];
    }

    public function githubApiException(ExceptionEvent $event): void
    {
        if (false === $event->isMasterRequest()
            || false === $event->getException() instanceof RuntimeException
        ) {
            return;
        };

        switch ($event->getException()->getCode()) {
            case Response::HTTP_UNAUTHORIZED:
                $this->flashBag->add('danger', 'Bad credentials. You must authorize Github OAuth2.');
                $event->setResponse(
                    new RedirectResponse(
                        $this->router->generate('home')
                    )
                );
                break;

            default:
                switch (\get_class($event->getException())) {
                    case ApiLimitExceedException::class:
                        $this->flashBag->add('danger', $event->getException()->getMessage());
                        $event->setResponse(
                            new RedirectResponse(
                                $this->router->generate('home')
                            )
                        );
                        break;
                }
                break;
        }
    }

    public function githubGuiException(ExceptionEvent $event): void
    {
        if (false === $event->isMasterRequest()
            || false === $event->getException() instanceof GithubGuiException
        ) {
            return;
        };

        switch ($event->getException()->getCode()) {
            case GithubGuiException::CODE_FILTERS_NOT_ENABLED:
            case GithubGuiException::CODE_FILTERS_ARE_EMPTY:
            case GithubGuiException::CODE_CONFIG_IS_EMPTY:
                $this->flashBag->add('danger', $event->getException()->getMessage());
                $event->setResponse(
                    new RedirectResponse(
                        $this->router->generate('user_configuration')
                    )
                );
                break;

            case GithubGuiException::CODE_FILTERS_NOT_ENABLED_XHR:
            case GithubGuiException::CODE_CONFIG_IS_EMPTY_XHR:
                $event->setResponse(
                    new JsonResponse(
                        [
                            'error' => [
                                'code' => $event->getException()->getCode(),
                                'message' => $event->getException()->getMessage(),
                            ],
                        ],
                        Response::HTTP_FORBIDDEN
                    )
                );
                break;
        }
    }

    public function xhrException(ExceptionEvent $event): void
    {
        if (false === $event->isMasterRequest()
            || false === $event->getException() instanceof XhrException
        ) {
            return;
        };

        $this->flashBag->add('danger', $event->getException()->getMessage());
        $event->setResponse(
            new RedirectResponse(
                $this->router->generate('home')
            )
        );
    }
}