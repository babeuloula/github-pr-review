<?php

declare(strict_types=1);

namespace App\Event\EventSubscriber;

use App\Exception\EmptyFilterException;
use App\Exception\FiltersNotEnabledException;
use App\Exception\MissingConfigurationException;
use App\Exception\XhrException;
use Github\Exception\ExceptionInterface as GithubException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ExceptionSubscriber implements EventSubscriberInterface
{
    public function __construct(readonly protected UrlGeneratorInterface $router)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => [
                ['githubException', 10],
                ['xhrException', 10],
            ],
        ];
    }

    public function githubException(ExceptionEvent $event): void
    {
        if ($event->getThrowable() instanceof GithubException
            || $event->getThrowable() instanceof EmptyFilterException
            || $event->getThrowable() instanceof FiltersNotEnabledException
            || $event->getThrowable() instanceof MissingConfigurationException
        ) {
            if (true === $event->getRequest()->isXmlHttpRequest()) {
                $response = new JsonResponse(
                    [
                        'error' => [
                            'code' => $event->getThrowable()->getCode(),
                            'message' => $event->getThrowable()->getMessage(),
                        ],
                    ],
                    Response::HTTP_FORBIDDEN
                );
            } else {
                $event->getRequest()->getSession()->getFlashBag()->add(
                    'error',
                    $event->getThrowable()->getMessage()
                );
                $response = new RedirectResponse(
                    $this->router->generate('user_configuration')
                );
            }

            $event->setResponse($response);
        }
    }

    public function xhrException(ExceptionEvent $event): void
    {
        if (false === $event->getThrowable() instanceof XhrException) {
            return;
        }

        $event->getRequest()->getSession()->getFlashBag()->add('error', $event->getThrowable()->getMessage());
        $event->setResponse(
            new RedirectResponse(
                $this->router->generate('home')
            )
        );
    }
}
