<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @license     Proprietary
 * @copyright   Copyright (c) Wizacha
 */
declare(strict_types=1);

namespace App\Controller;

use App\Service\Github\PullRequestFilterService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class ReloadPullRequestController
{
    /** @var PullRequestFilterService */
    protected $pullRequestFilterService;

    /** @var bool */
    protected $useFilters;

    /** @var Environment */
    protected $twig;

    public function __construct(
        PullRequestFilterService $pullRequestFilterService,
        bool $useFilters,
        Environment $twig
    ) {
        $this->pullRequestFilterService = $pullRequestFilterService;
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
                'github/pull-request/filtersPullRequests.html.twig',
                [
                    'openPullRequests' => $this->pullRequestFilterService->getOpen(),
                    'openPullRequestsCount' => $this->pullRequestFilterService->getOpenCount(),
                ]
            )
        );
    }
}
