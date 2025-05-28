<?php

namespace VeeZions\BuilderEngine\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use VeeZions\BuilderEngine\Constant\ConfigConstant;

final class GlobalVariablesCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('twig')) {
            return;
        }

        $twigDefinition = $container->getDefinition('twig');

        $twigDefinition
            ->addMethodCall('addGlobal', [ConfigConstant::CONFIG_FILE_NAME, new Reference('veezions_builder_engine.twig.global')]);
    }
}
