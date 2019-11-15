<?php

/**
 * @author BaBeuloula <info@babeuloula.fr>
 */

declare(strict_types=1);

namespace App\Controller\User;

use App\Entity\User;
use App\Enum\Color;
use App\Enum\NotificationType;
use App\Factory\ConfigurationFactory;
use App\Repository\ConfigurationRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Twig\Environment;

final class ConfigurationController
{
    /** @var Environment */
    private $twig;

    /** @var ConfigurationFactory */
    private $configurationFactory;

    /** @var ConfigurationRepository */
    private $configurationRepository;

    /** @var FlashBagInterface */
    private $flashBag;

    /** @var UrlGeneratorInterface */
    private $router;

    public function __construct(
        Environment $twig,
        ConfigurationFactory $configurationFactory,
        ConfigurationRepository $configurationRepository,
        FlashBagInterface $flashBag,
        UrlGeneratorInterface $router
    ) {
        $this->twig = $twig;
        $this->configurationFactory = $configurationFactory;
        $this->configurationRepository = $configurationRepository;
        $this->flashBag = $flashBag;
        $this->router = $router;
    }

    /** @param User $user */
    public function __invoke(UserInterface $user, Request $request): Response
    {
        if ('POST' === $request->getMethod()) {
            $this->configurationRepository->save(
                $this
                    ->configurationFactory
                    ->createFromRequest($request, $user->getConfiguration())
                    ->setUser($user)
            );

            $this->flashBag->add('success', 'Configuration saved with success.');

            return new RedirectResponse(
                $this->router->generate('user_configuration')
            );
        }

        return new Response(
            $this->twig->render(
                'user/configuration.html.twig',
                [
                    'configuration' => $user->getConfiguration() ?? $this->configurationFactory->createDefault(),
                    'colors' => Color::toArray(),
                    'notificationType' => NotificationType::toArray()
                ]
            )
        );
    }
}
