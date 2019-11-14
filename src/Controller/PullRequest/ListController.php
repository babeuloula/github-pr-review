<?php

/**
 * @author BaBeuloula <info@babeuloula.fr>
 */

declare(strict_types=1);

namespace App\Controller\PullRequest;

use App\Entity\Configuration;
use App\Entity\User;
use App\Enum\Label;
use App\Enum\UseMode;
use App\Service\Github\NotificationService;
use App\Service\Github\PullRequestFilterService;
use App\Service\Github\PullRequestLabelService;
use App\Service\Github\PullRequestServiceInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Twig\Environment;

final class ListController
{
    /** @var PullRequestLabelService */
    private $pullRequestLabelService;

    /** @var PullRequestFilterService */
    private $pullRequestFilterService;

    /** @var NotificationService */
    private $notificationService;

    /** @var UseMode */
    private $useMode;

    /** @var Environment */
    private $twig;

    /** @var UrlGeneratorInterface */
    private $router;

    public function __construct(
        PullRequestLabelService $pullRequestLabelService,
        PullRequestFilterService $pullRequestFilterService,
        NotificationService $notificationService,
        string $useMode,
        Environment $twig,
        UrlGeneratorInterface $router
    ) {
        $this->pullRequestLabelService = $pullRequestLabelService;
        $this->pullRequestFilterService = $pullRequestFilterService;
        $this->notificationService = $notificationService;
        $this->useMode = new UseMode($useMode);
        $this->twig = $twig;
        $this->router = $router;
    }

    /** @param User $user */
    public function __invoke(UserInterface $user): Response
    {
        $configuration = $user->getConfiguration();

        if (false === $configuration instanceof Configuration) {
            return new RedirectResponse(
                $this->router->generate('user_configuration')
            );
        }

        /** @var PullRequestServiceInterface $service */
        $service = (true === $this->useMode->equals(UseMode::LABEL()))
            ? $this->pullRequestLabelService
            : $this->pullRequestFilterService;

        return new Response(
            $this->twig->render(
                'pull-request/list.html.twig',
                [
                    'openPullRequests' => $service->getOpen(),
                    'unreadNotifications' => $this->notificationService->getNotifications(),
                    'unreadNotificationsCount' => $this->notificationService->getNotificationsCount(),
                    'labels' => Label::toArray(),
                ]
            )
        );
    }
}
