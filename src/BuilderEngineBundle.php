<?php

namespace VeeZions\BuilderEngine;

use Doctrine\ORM\EntityManager;
use Symfony\Component\AssetMapper\AssetMapperInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Twig\Environment;
use VeeZions\BuilderEngine\DependencyInjection\Compiler\GlobalVariablesCompilerPass;
use Gedmo\Sluggable\SluggableListener;

class BuilderEngineBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        if ($this->isAssetMapperAvailable()) {
            $container->prependExtensionConfig('framework', [
                'asset_mapper' => [
                    'paths' => [
                        __DIR__.'/../assets/controllers' => '@veezions/builder-engine-bundle',
                        __DIR__.'/../assets/js' => '@veezions/builder-engine-bundle',
                        __DIR__.'/../assets/utils' => '@veezions/builder-engine-bundle',
                        __DIR__.'/../assets/libraries' => '@veezions/builder-engine-bundle',
                        __DIR__.'/../assets/css' => '@veezions/builder-engine-bundle'
                    ],
                ],
            ]);
        }

        if ($this->isDoctrineAvailable()) {
            $container->prependExtensionConfig('doctrine', [
                'orm' => [
                    'mappings' => [
                        'BuilderEngineBundle' => [
                            'is_bundle' => true,
                            'type' => 'attribute',
                            'dir' => 'src',
                            'prefix' => 'VeeZions\BuilderEngine',
                        ],
                    ],
                ],
            ]);
        }

        if ($this->isTwigAvailable()) {
            $paths = [
                '%kernel.project_dir%/vendor/veezions/builder-engine-bundle/src/Resources/internal' => 'BuilderEngineInternal',
                '%kernel.project_dir%/vendor/veezions/builder-engine-bundle/src/Resources/views' => 'BuilderEngineBundle',
            ];

            $filesystem = new Filesystem();
            $templatesPath = $container
                ->getParameterBag()
                ->resolveValue('%kernel.project_dir%/templates/bundles/BuilderEngineBundle');

            if ($filesystem->exists($templatesPath)) {
                $paths['%kernel.project_dir%/templates/bundles/BuilderEngineBundle'] = 'BuilderEngineBundle';
                $paths = array_reverse($paths);
            }

            $container->prependExtensionConfig('twig', [
                'paths' => $paths
            ]);
        }
        
        if ($this->isStofBundleAvailable()) {
            $container->prependExtensionConfig('stof_doctrine_extensions', [
                'orm' => [
                    'default' => [ 
                        'sluggable' => true,
                        'timestampable' => true,
                        'translatable' => true,
                        'tree' => true,
                        'sortable' => true,
                        'loggable' => true,
                        'blameable' => true,
                        'softdeleteable' => true,
                    ]
                ],
            ]);
        }

        $container->addCompilerPass(new GlobalVariablesCompilerPass());
    }

    public function getPath(): string
    {
        return \dirname(__DIR__);
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
    
    private function isStofBundleAvailable(): bool
    {
        return ContainerBuilder::willBeAvailable('stof/doctrine-extensions-bundle', SluggableListener::class, ['doctrine/doctrine-bundle']);
    }
}
