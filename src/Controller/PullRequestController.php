<?php
/**
 * @author BaBeuloula <info@babeuloula.fr>
 */

declare(strict_types=1);

namespace App\Controller;

use App\Enum\Label;
use App\Service\Github\NotificationService;
use App\Service\Github\PullRequestFilterService;
use App\Service\Github\PullRequestLabelService;
use App\Service\Github\PullRequestServiceInterface;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class PullRequestController
{
    /** @var PullRequestLabelService */
    protected $pullRequestLabelService;

    /** @var PullRequestFilterService */
    protected $pullRequestFilterService;

    /** @var NotificationService */
    protected $notificationService;

    /** @var bool */
    protected $useLabels;

    /** @var bool */
    protected $useFilters;

    /** @var Environment */
    protected $twig;

    public function __construct(
        PullRequestLabelService $pullRequestLabelService,
        PullRequestFilterService $pullRequestFilterService,
        NotificationService $notificationService,
        bool $useLabels,
        bool $useFilters,
        Environment $twig
    ) {
        $this->pullRequestLabelService = $pullRequestLabelService;
        $this->pullRequestFilterService = $pullRequestFilterService;
        $this->notificationService = $notificationService;
        $this->useLabels = $useLabels;
        $this->useFilters = $useFilters;
        $this->twig = $twig;
    }

    public function __invoke(): Response
    {
        if ((false === $this->useLabels && false === $this->useFilters)
            || (true === $this->useLabels && true === $this->useFilters)
        ) {
            throw new \RuntimeException("You must choose between labels or filters.");
        }

        /** @var PullRequestServiceInterface $service */
        $service = $this->useLabels ? $this->pullRequestLabelService : $this->pullRequestFilterService;

        return new Response(
            $this->twig->render(
                'github/pull-request/list.html.twig',
                [
                    'openPullRequests' => $service->getOpen(),
                    'openPullRequestsCount' => $service->getOpenCount(),
                    'unreadNotifications' => $this->notificationService->getNotifications(),
                    'unreadNotificationsCount' => $this->notificationService->getNotificationsCount(),
                    'labels' => Label::toArray(),
                ]
            )
        );
    }
}
