<?php

declare(strict_types=1);

namespace App\Controller\PullRequest;

use App\Contract\Service\PullRequestServiceInterface;
use App\Entity\User;
use App\Enum\Label;
use App\Exception\EmptyFilterException;
use App\Exception\MissingConfigurationException;
use App\Service\Github\NotificationService;
use App\Service\Github\PullRequestFilterService;
use App\Service\Github\PullRequestLabelService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Twig\Environment;

final class ListController
{
    public function __construct(
        private readonly PullRequestLabelService $pullRequestLabelService,
        private readonly PullRequestFilterService $pullRequestFilterService,
        private readonly NotificationService $notificationService,
        private readonly Environment $twig,
    ) {
    }

    /** @param User $user */
    #[Route('/pull-requests', name: 'pull_requests_list', methods: Request::METHOD_GET)]
    public function __invoke(UserInterface $user): Response
    {
        if (0 === \count($user->getConfiguration()->getRepositories())) {
            throw new MissingConfigurationException();
        }

        if (true === $user->getConfiguration()->getMode()->isFilter()
            && 0 === \count($user->getConfiguration()->getFilters())
        ) {
            throw new EmptyFilterException();
        }

        /** @var PullRequestServiceInterface $service */
        $service = (true === $user->getConfiguration()->getMode()->isLabel())
            ? $this->pullRequestLabelService
            : $this->pullRequestFilterService;

        return new Response(
            $this->twig->render(
                'pull-request/list.html.twig',
                [
                    'openPullRequests' => $service->getOpen(),
                    'unreadNotifications' => $this->notificationService->getNotifications(),
                    'unreadNotificationsCount' => $this->notificationService->getNotificationsCount(),
                    'labels' => Label::cases(),
                ]
            )
        );
    }
}
