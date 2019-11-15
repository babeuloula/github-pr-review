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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Twig\Environment;

class ReloadController
{
    /** @var NotificationService */
    protected $notificationService;

    /** @var Environment */
    protected $twig;

    public function __construct(
        NotificationService $notificationService,
        Environment $twig
    ) {
        $this->notificationService = $notificationService;
        $this->twig = $twig;
    }

    /** @param User $user */
    public function __invoke(Request $request, UserInterface $user): Response
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

        return new Response(
            $this->twig->render(
                'pull-request/filtersNotifications.html.twig',
                [
                    'unreadNotifications' => $this->notificationService->getNotifications(),
                    'unreadNotificationsCount' => $this->notificationService->getNotificationsCount(),
                ]
            )
        );
    }
}
