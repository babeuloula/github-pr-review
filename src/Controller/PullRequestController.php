<?php
/**
 * @author BaBeuloula <info@babeuloula.fr>
 */

declare(strict_types=1);

namespace App\Controller;

use App\Enum\Label;
use App\Enum\UseMode;
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

    /** @var UseMode */
    protected $useMode;

    /** @var Environment */
    protected $twig;

    public function __construct(
        PullRequestLabelService $pullRequestLabelService,
        PullRequestFilterService $pullRequestFilterService,
        NotificationService $notificationService,
        string $useMode,
        Environment $twig
    ) {
        $this->pullRequestLabelService = $pullRequestLabelService;
        $this->pullRequestFilterService = $pullRequestFilterService;
        $this->notificationService = $notificationService;
        $this->useMode = new UseMode($useMode);
        $this->twig = $twig;
    }

    public function __invoke(): Response
    {
        /** @var PullRequestServiceInterface $service */
        $service = $this->useMode->equals(UseMode::LABEL())
            ? $this->pullRequestLabelService
            : $this->pullRequestFilterService;

        return new Response(
            $this->twig->render(
                'github/pull-request/list.html.twig',
                [
                    'openPullRequests' => $service->getOpen(),
                    'unreadNotifications' => $this->notificationService->getNotifications(),
                    'labels' => Label::toArray(),
                ]
            )
        );
    }
}
