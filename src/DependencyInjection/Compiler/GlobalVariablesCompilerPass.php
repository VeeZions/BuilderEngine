<?php

namespace XenoLab\XenoEngine\DependencyInjection\Compiler;

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
            ->addMethodCall('addGlobal', ['xeno', new Reference('xenolab_xeno_engine.twig.global')]);
    }
}
