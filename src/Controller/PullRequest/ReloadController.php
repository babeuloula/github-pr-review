<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @license     Proprietary
 * @copyright   Copyright (c) Wizacha
 */

declare(strict_types=1);

namespace App\Controller\PullRequest;

use App\Entity\Configuration;
use App\Entity\User;
use App\Enum\UseMode;
use App\Exception\GithubGuiException;
use App\Exception\XhrException;
use App\Service\Github\PullRequestFilterService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Twig\Environment;

final class ReloadController
{
    private PullRequestFilterService $pullRequestFilterService;

    private Environment $twig;

    public function __construct(
        PullRequestFilterService $pullRequestFilterService,
        Environment $twig
    ) {
        $this->pullRequestFilterService = $pullRequestFilterService;
        $this->twig = $twig;
    }

    /** @param User $user */
    public function __invoke(Request $request, UserInterface $user): Response
    {
        if (false === $user->getConfiguration() instanceof Configuration
        ) {
            throw new GithubGuiException(
                GithubGuiException::MESSAGE_CONFIG_IS_EMPTY,
                GithubGuiException::CODE_CONFIG_IS_EMPTY
            );
        }

        if (false === UseMode::FILTER()->equals($user->getConfiguration()->getMode())) {
            throw new GithubGuiException(
                GithubGuiException::MESSAGE_FILTERS_NOT_ENABLED,
                GithubGuiException::CODE_FILTERS_NOT_ENABLED
            );
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
