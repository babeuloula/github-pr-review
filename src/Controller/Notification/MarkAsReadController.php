<?php

declare(strict_types=1);

namespace App\Controller\Notification;

use App\Entity\User;
use App\Exception\FiltersNotEnabledException;
use App\Exception\MissingConfigurationException;
use App\Exception\XhrException;
use App\Service\Github\NotificationService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

final class MarkAsReadController
{
    public function __construct(
        readonly private NotificationService $notificationService,
    ) {
    }

    /** @param User $user */
    // phpcs:ignore
    #[Route('/notifications/mark-as-read/{threadId}', name: 'notification_mark_as_read', requirements: ['threadId' => '\d+'], methods: Request::METHOD_POST)]
    public function __invoke(Request $request, UserInterface $user, int $threadId): JsonResponse
    {
        if (0 === \count($user->getConfiguration()->getRepositories())) {
            throw new MissingConfigurationException();
        }

        if (false === $user->getConfiguration()->getMode()->isFilter()) {
            throw new FiltersNotEnabledException();
        }

        if (false === $request->isXmlHttpRequest()) {
            throw new XhrException();
        }

        return new JsonResponse(
            status: (true === $this->notificationService->markAsRead($threadId)) ? 200 : 500
        );
    }
}
