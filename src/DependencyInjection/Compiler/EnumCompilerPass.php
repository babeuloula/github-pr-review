<?php

declare(strict_types=1);

namespace App\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class EnumCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $typesDefinition = [];
        if (true === $container->hasParameter('doctrine.dbal.connection_factory.types')) {
            $typesDefinition = $container->getParameter('doctrine.dbal.connection_factory.types');
        }

        $taggedEnums = $container->findTaggedServiceIds('app.doctrine.type.enum');

        // phpcs:ignore
        foreach ($taggedEnums as $enumType => $definition) {
            $typesDefinition[$enumType::NAME] = ['class' => $enumType];
        }

        $container->setParameter('doctrine.dbal.connection_factory.types', $typesDefinition);
    }
}
