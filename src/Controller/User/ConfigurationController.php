<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Entity\User;
use App\Enum\Color;
use App\Enum\NotificationReason;
use App\Factory\ConfigurationFactory;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Twig\Environment;

final class ConfigurationController
{
    public function __construct(
        private readonly Environment $twig,
        private readonly UserRepository $repository,
        private readonly UrlGeneratorInterface $router
    ) {
    }

    /** @param User $user */
    #[Route('/user/configuration', name: 'user_configuration', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function __invoke(UserInterface $user, Request $request): Response
    {
        if (Request::METHOD_POST === $request->getMethod()) {
            $this->repository->add(
                $user->setConfiguration(
                    ConfigurationFactory::createFromRequest($request, $user->getConfiguration())
                ),
                true,
            );

            /** @var Session $session */
            $session = $request->getSession();
            $session->getFlashBag()->add('success', 'Configuration saved with success.');

            return new RedirectResponse(
                $this->router->generate('user_configuration')
            );
        }

        return new Response(
            $this->twig->render(
                'user/configuration.html.twig',
                [
                    'configuration' => $user->getConfiguration(),
                    'colors' => Color::cases(),
                    'allowedColors' => array_map(
                        static function (Color $color): string {
                            return $color->value;
                        },
                        Color::cases(),
                    ),
                    'notificationReasons' => NotificationReason::cases(),
                ]
            )
        );
    }
}
