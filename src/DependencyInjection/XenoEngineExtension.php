<?php

namespace XenoLab\XenoEngine\DependencyInjection;

use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Alias;

class XenoEngineExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new PhpFileLoader(
            $container,
            new FileLocator(__DIR__.'/../../config')
        );
        $loader->load('services.php');

        $this->setServicesArguments($config, $container);
    }

    public function getConfiguration(array $config, ContainerBuilder $container): XenoEngineConfiguration
    {
        return new XenoEngineConfiguration();
    }

    private function setServicesArguments(array $config, ContainerBuilder $container): void
    {
        $controlerDefinition = $container->getDefinition('xenolab_xeno_engine.engine_controller');
        $controlerDefinition->setArgument('$clientId', $config['twitter']['client_id']);
    }
}
