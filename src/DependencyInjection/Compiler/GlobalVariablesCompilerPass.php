<?php

namespace Vision\BuilderEngine\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class GlobalVariablesCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('twig')) {
            return;
        }

        $twigDefinition = $container->getDefinition('twig');

        $twigDefinition
            ->addMethodCall('addGlobal', ['builder', new Reference('vision_builder_engine.twig.global')]);
    }
}
