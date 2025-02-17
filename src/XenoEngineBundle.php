<?php

namespace XenoLabs\XenoEngine;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\AssetMapper\AssetMapperInterface;
use Doctrine\ORM\EntityManager;
use function dirname;

class XenoEngineBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        if ($this->isAssetMapperAvailable()) {

            $container->prependExtensionConfig('framework', [
                'asset_mapper' => [
                    'paths' => [
                        __DIR__ . '/../assets/controllers' => '@xenolabs/xeno-engine-bundle',
                        __DIR__ . '/../assets/js' => '@xenolabs/xeno-engine-bundle',
                        __DIR__ . '/../assets/css' => '@xenolabs/xeno-engine-bundle',
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
                            'prefix' => 'Xenolabs\XenoEngine'
                        ]
                    ]
                ],
            ]);
        }
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
}
