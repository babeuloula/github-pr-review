<?php

/**
 * @author BaBeuloula <info@babeuloula.fr>
 */

declare(strict_types=1);

namespace App\Controller;

use App\Enum\UseMode;
use App\Service\Github\NotificationService;
use Symfony\Component\HttpFoundation\{
    Response,
    Request
};
use Twig\Environment;

class ReloadNotificationController
{
    /** @var NotificationService */
    protected $notificationService;

    /** @var UseMode */
    protected $useMode;

    /** @var Environment */
    protected $twig;

    public function __construct(
        NotificationService $notificationService,
        string $useMode,
        Environment $twig
    ) {
        $this->notificationService = $notificationService;
        $this->useMode = new UseMode($useMode);
        $this->twig = $twig;
    }

    public function __invoke(Request $request): Response
    {
        if (false === $this->useMode->equals(UseMode::FILTER())) {
            throw new \RuntimeException("You need to use filters to access to this endpoint.");
        }

        if (false === $request->isXmlHttpRequest()) {
            throw new \RuntimeException("You must call this endpoint with XHR.");
        }

        return new Response(
            $this->twig->render(
                'github/pull-request/filtersNotifications.html.twig',
                [
                    'unreadNotifications' => $this->notificationService->getNotifications(),
                    'unreadNotificationsCount' => $this->notificationService->getNotificationsCount(),
                ]
            )
        );
    }
}
