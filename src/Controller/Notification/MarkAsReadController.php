<?php

/**
 * @author BaBeuloula <info@babeuloula.fr>
 */

declare(strict_types=1);

namespace App\Controller\Notification;

use App\Entity\Configuration;
use App\Entity\User;
use App\Exception\GithubGuiException;
use App\Exception\XhrException;
use App\Service\Github\NotificationService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Twig\Environment;

final class MarkAsReadController
{
    /** @var NotificationService */
    private $notificationService;

    /** @var Environment */
    private $twig;

    public function __construct(
        NotificationService $notificationService,
        Environment $twig
    ) {
        $this->notificationService = $notificationService;
        $this->twig = $twig;
    }

    /** @param User $user */
    public function __invoke(Request $request, UserInterface $user, int $threadId): JsonResponse
    {
        if (false === $user->getConfiguration() instanceof Configuration) {
            throw new GithubGuiException(
                GithubGuiException::MESSAGE_CONFIG_IS_EMPTY,
                GithubGuiException::CODE_CONFIG_IS_EMPTY_XHR
            );
        }

        if ('filter' === $user->getConfiguration()->getMode()) {
            throw new GithubGuiException(
                GithubGuiException::MESSAGE_FILTERS_NOT_ENABLED,
                GithubGuiException::CODE_FILTERS_NOT_ENABLED_XHR
            );
        }

        if (false === $request->isXmlHttpRequest()) {
            throw new XhrException();
        }

        return new JsonResponse(
            null,
            (true === $this->notificationService->markAsRead($threadId)) ? 200 : 500
        );
    }
}
