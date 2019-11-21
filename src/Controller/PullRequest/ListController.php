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
use App\Exception\GithubGuiException;
use App\Service\Github\NotificationService;
use App\Service\Github\PullRequestFilterService;
use App\Service\Github\PullRequestLabelService;
use App\Service\Github\PullRequestServiceInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
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

    /** @var Environment */
    private $twig;

    /** @var UrlGeneratorInterface */
    private $router;

    /** @var FlashBagInterface */
    private $flashBag;

    public function __construct(
        PullRequestLabelService $pullRequestLabelService,
        PullRequestFilterService $pullRequestFilterService,
        NotificationService $notificationService,
        Environment $twig,
        UrlGeneratorInterface $router,
        FlashBagInterface $flashBag
    ) {
        $this->pullRequestLabelService = $pullRequestLabelService;
        $this->pullRequestFilterService = $pullRequestFilterService;
        $this->notificationService = $notificationService;
        $this->twig = $twig;
        $this->router = $router;
        $this->flashBag = $flashBag;
    }

    /** @param User $user */
    public function __invoke(UserInterface $user): Response
    {
        if (false === $user->getConfiguration() instanceof Configuration) {
            throw new GithubGuiException(
                GithubGuiException::MESSAGE_CONFIG_IS_EMPTY,
                GithubGuiException::CODE_CONFIG_IS_EMPTY
            );
        }

        if (true === UseMode::FILTER()->equals($user->getConfiguration()->getMode())
            && 0 === \count($user->getConfiguration()->getFilters())
        ) {
            throw new GithubGuiException(
                GithubGuiException::MESSAGE_FILTERS_ARE_EMPTY,
                GithubGuiException::CODE_FILTERS_ARE_EMPTY
            );
        }

        /** @var PullRequestServiceInterface $service */
        $service = (true === UseMode::LABEL()->equals($user->getConfiguration()->getMode()))
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
