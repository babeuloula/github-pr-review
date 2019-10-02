<?php
/**
 * @author BaBeuloula <info@babeuloula.fr>
 */

declare(strict_types=1);

namespace App\Controller;

use App\Service\Github\NotificationService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class ReloadNotificationController
{
    /** @var NotificationService */
    protected $notificationService;

    /** @var bool */
    protected $useFilters;

    /** @var Environment */
    protected $twig;

    public function __construct(
        NotificationService $notificationService,
        bool $useFilters,
        Environment $twig
    ) {
        $this->notificationService = $notificationService;
        $this->useFilters = $useFilters;
        $this->twig = $twig;
    }

    public function __invoke(Request $request): Response
    {
        if (false === $this->useFilters) {
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
