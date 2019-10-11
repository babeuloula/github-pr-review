<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @license     Proprietary
 * @copyright   Copyright (c) Wizacha
 */
declare(strict_types=1);

namespace App\Controller;

use App\Enum\UseMode;
use App\Service\Github\PullRequestFilterService;
use Symfony\Component\{
    HttpFoundation\Response,
    HttpFoundation\Request
};
use Twig\Environment;

class ReloadPullRequestController
{
    /** @var PullRequestFilterService */
    protected $pullRequestFilterService;

    /** @var UseMode */
    protected $useMode;

    /** @var Environment */
    protected $twig;

    public function __construct(
        PullRequestFilterService $pullRequestFilterService,
        string $useMode,
        Environment $twig
    ) {
        $this->pullRequestFilterService = $pullRequestFilterService;
        $this->useMode = new UseMode($useMode);
        $this->twig = $twig;
    }

    public function __invoke(Request $request): Response
    {
        if (false === $this->useMode->equals(UseMode::FILTER())) {
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
                ]
            )
        );
    }
}
