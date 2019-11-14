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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Twig\Environment;

final class ConfigurationController
{
    /** @var Environment */
    private $twig;

    /** @var ConfigurationFactory */
    private $configurationFactory;

    public function __construct(Environment $twig, ConfigurationFactory $configurationFactory)
    {
        $this->twig = $twig;
        $this->configurationFactory = $configurationFactory;
    }

    /** @param User $user */
    public function __invoke(UserInterface $user): Response
    {
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
