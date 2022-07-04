<?php

declare(strict_types=1);

namespace App\Controller\PullRequest;

use App\Entity\User;
use App\Exception\FiltersNotEnabledException;
use App\Exception\MissingConfigurationException;
use App\Exception\XhrException;
use App\Service\Github\PullRequestFilterService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Twig\Environment;

final class ReloadController
{
    public function __construct(
        readonly private PullRequestFilterService $pullRequestFilterService,
        readonly private Environment $twig
    ) {
    }

    /** @param User $user */
    #[Route('/pull-requests/reload', name: 'pull_requests_reload', methods: Request::METHOD_GET)]
    public function __invoke(Request $request, UserInterface $user): Response
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

        return new Response(
            $this->twig->render(
                'pull-request/filtersPullRequests.html.twig',
                [
                    'openPullRequests' => $this->pullRequestFilterService->getOpen(),
                ]
            )
        );
    }
}
