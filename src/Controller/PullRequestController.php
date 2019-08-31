<?php
/**
 * @author BaBeuloula <info@babeuloula.fr>
 */
declare(strict_types=1);

namespace App\Controller;

use App\Enum\Label;
use App\Service\Github\PullRequestService;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class PullRequestController
{
    /** @var PullRequestService */
    protected $pullRequestService;

    /** @var Environment */
    protected $twig;

    public function __construct(PullRequestService $pullRequestService, Environment $twig)
    {
        $this->pullRequestService = $pullRequestService;
        $this->twig = $twig;
    }

    public function __invoke(): Response
    {
        return new Response(
            $this->twig->render(
                'github/pull-request/list.html.twig',
                [
                    'openPullRequests' => $this->pullRequestService->getOpen(),
                    'labels' => Label::toArray(),
                ]
            )
        );
    }
}
