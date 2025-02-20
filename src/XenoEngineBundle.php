<?php

namespace XenoLab\XenoEngine;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\AssetMapper\AssetMapperInterface;
use Doctrine\ORM\EntityManager;
use Twig\Environment;
use XenoLab\XenoEngine\DependencyInjection\Compiler\GlobalVariablesCompilerPass;
use function dirname;

class XenoEngineBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        if ($this->isAssetMapperAvailable()) {

            $container->prependExtensionConfig('framework', [
                'asset_mapper' => [
                    'paths' => [
                        __DIR__ . '/../assets/controllers' => '@xenolab/xeno-engine-bundle',
                        __DIR__ . '/../assets/js' => '@xenolab/xeno-engine-bundle',
                        __DIR__ . '/../assets/utils' => '@xenolab/xeno-engine-bundle',
                        __DIR__ . '/../assets/css' => '@xenolab/xeno-engine-bundle'
                    ],
                ],
            ]);
        }

        if ($this->isDoctrineAvailable()) {
            
            $container->prependExtensionConfig('doctrine', [
                'orm' => [
                    'mappings' => [
                        'XenoEngineBundle' => [
                            'is_bundle' => true,
                            'type' => 'attribute',
                            'dir' => 'src',
                            'prefix' => 'XenoLab\XenoEngine'
                        ]
                    ]
                ],
            ]);
        }

        if ($this->isTwigAvailable()) {

            $container->prependExtensionConfig('twig', [
                'paths' => [
                    '%kernel.project_dir%/templates/bundles/XenoEngineBundle' => 'XenoEngineBundle',
                    '%kernel.project_dir%/vendor/xenolab/xeno-engine-bundle/src/Resources/views' => 'XenoEngineBundle',
                    '%kernel.project_dir%/vendor/xenolab/xeno-engine-bundle/src/Resources/internal' => 'XenoEngineAsynchronousInternal',
                ],
            ]);
        }

        $container->addCompilerPass(new GlobalVariablesCompilerPass());
    }

    public function getPath(): string
    {
        return dirname(__DIR__);
    }

    private function isAssetMapperAvailable(): bool
    {
        return ContainerBuilder::willBeAvailable('symfony/asset-mapper', AssetMapperInterface::class, ['symfony/framework-bundle']);
    }

    private function isDoctrineAvailable(): bool
    {
        return ContainerBuilder::willBeAvailable('doctrine/orm', EntityManager::class, ['doctrine/doctrine-bundle']);
    }

    private function isTwigAvailable(): bool
    {
        return ContainerBuilder::willBeAvailable('twig/environment', Environment::class, ['symfony/framework-bundle']);
    }
}
