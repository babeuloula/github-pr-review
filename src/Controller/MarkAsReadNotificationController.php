<?php

/**
 * @author BaBeuloula <info@babeuloula.fr>
 */

declare(strict_types=1);

namespace App\Controller;

use App\Enum\UseMode;
use App\Service\Github\NotificationService;
use Symfony\Component\HttpFoundation\{
    JsonResponse,
    Request
};
use Twig\Environment;

class MarkAsReadNotificationController
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

    public function __invoke(Request $request, int $threadId): JsonResponse
    {
        if (false === $this->useMode->equals(UseMode::FILTER())) {
            throw new \RuntimeException("You need to use filters to access to this endpoint.");
        }

        if (false === $request->isXmlHttpRequest()) {
            throw new \RuntimeException("You must call this endpoint with XHR.");
        }

        return new JsonResponse(
            null,
            (true === $this->notificationService->markAsRead($threadId)) ? 200 : 500
        );
    }
}
