<?php

namespace XenoLabs\XenoEngine;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\AssetMapper\AssetMapperInterface;
use Doctrine\ORM\EntityManager;

class XenoEngineBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        if ($this->isAssetMapperAvailable($container)) {

            $this->addImportJsFileInAppJs();
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

        if ($this->isDoctrineAvailable($container)) {
            
            $container->prependExtensionConfig('doctrine', [
                'orm' => [
                    'mappings' => [
                        'XenoEngineBundle' => [
                            'is_bundle' => true,
                            'type' => 'attribute',
                            'dir' => 'src/Entity',
                            'prefix' => 'Xenolabs\XenoEngineBundle\Entity',
                            'alias' => 'Xenolabs\XenoEngineBundle',
                        ]
                    ]
                ],
            ]);
        }
    }

    public function getPath(): string
    {
        return \dirname(__DIR__);
    }

    private function isAssetMapperAvailable(ContainerBuilder $container): bool
    {
        if (!interface_exists(AssetMapperInterface::class)) {
            return false;
        }

        $bundlesMetadata = $container->getParameter('kernel.bundles_metadata');
        if (!isset($bundlesMetadata['FrameworkBundle'])) {
            return false;
        }

        return is_file($bundlesMetadata['FrameworkBundle']['path'] . '/Resources/config/asset_mapper.php');
    }

    private function isDoctrineAvailable(ContainerBuilder $container): bool
    {
        return class_exists(EntityManager::class);
    }

    private function addImportJsFileInAppJs(): void
    {
        $appJsFile = \dirname(__DIR__, 4) . '/assets/app.js';
        $newLine = "import '@xenolabs/xeno-engine';" . "\n";
        
        if (file_exists($appJsFile)) {
            $lines = file($appJsFile);
            if (!in_array($newLine, $lines)) {
                array_unshift($lines, "import '@xenolabs/xeno-engine';" . "\n");
                $file = fopen($appJsFile, "w+");
                foreach($lines as $line){
                    fwrite($file, $line);
                }
                fclose($file);
            }
        }
    }
}
