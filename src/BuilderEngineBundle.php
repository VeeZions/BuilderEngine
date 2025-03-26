<?php

namespace VeeZions\BuilderEngine;

use Doctrine\ORM\EntityManager;
use League\Flysystem\Filesystem as OneupFlysystem;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\AssetMapper\AssetMapperInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Twig\Environment;
use VeeZions\BuilderEngine\Constant\ConfigConstant;
use VeeZions\BuilderEngine\DependencyInjection\Compiler\GlobalVariablesCompilerPass;
use Gedmo\Sluggable\SluggableListener;
use VeeZions\BuilderEngine\Provider\PackageConfigProvider;

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

        $liipConfig = PackageConfigProvider::getConfigFileFromFileName(
            $container, 
            ConfigConstant::CONFIG_LIIP_FILE_NAME
        );
        
        $oneupConfig = PackageConfigProvider::getConfigFileFromFileName(
            $container, 
            ConfigConstant::CONFIG_ONEUP_FILE_NAME
        );
        
        $config = PackageConfigProvider::getConfigFileFromFileName(
            $container, 
            ConfigConstant::CONFIG_FILE_NAME
        );

        if ($this->isLiipImagineBundleAvailable($liipConfig) && $this->isOneupFlysystemBundleAvailable($oneupConfig)) {

            if (PackageConfigProvider::isLocaleDriver($config, $liipConfig, $oneupConfig)) {
                $container->prependExtensionConfig(
                    ConfigConstant::CONFIG_ONEUP_FILE_NAME, 
                    PackageConfigProvider::setOneupFlysystemLocaleConfiguration()
                );
                
                $container->prependExtensionConfig(
                    ConfigConstant::CONFIG_LIIP_FILE_NAME, 
                    PackageConfigProvider::setLiipImagineLocaleConfiguration($liipConfig, $config)
                );
            }

            if (PackageConfigProvider::isS3Driver($config, $liipConfig, $oneupConfig)) {
                $container->prependExtensionConfig(
                    ConfigConstant::CONFIG_ONEUP_FILE_NAME, 
                    PackageConfigProvider::setOneupFlysystemS3Configuration($config)
                );
                
                $container->prependExtensionConfig(
                    ConfigConstant::CONFIG_LIIP_FILE_NAME, 
                    PackageConfigProvider::setLiipImagineS3Configuration($liipConfig, $config)
                );
            }
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

    private function isLiipImagineBundleAvailable(array $config = []): bool
    {
        return ContainerBuilder::willBeAvailable('liip/imagine-bundle', CacheManager::class, []) && !empty($config);
    }

    private function isOneupFlysystemBundleAvailable(array $config = []): bool
    {
        return ContainerBuilder::willBeAvailable('oneup/flysystem-bundle', OneupFlysystem::class, []) && !empty($config);
    }
}
