<?php

namespace XenoLab\XenoEngine\DependencyInjection;

use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Alias;

class XenoEngineExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new PhpFileLoader(
            $container,
            new FileLocator(__DIR__.'/../../config')
        );
        $loader->load('services.php');

        if ($container->getParameter('kernel.debug') === true) {
            $loader->load('debug.php');
        }

        $this->setControllersArgumentsFromConfig($config, $container);
    }

    public function getConfiguration(array $config, ContainerBuilder $container): XenoEngineConfiguration
    {
        return new XenoEngineConfiguration();
    }

    private function setControllersArgumentsFromConfig(array $config, ContainerBuilder $container): void
    {
        $asyncDefinition = $container->getDefinition('xenolab_xeno_engine.route_loader');
        $asyncDefinition->setArgument('$mode', $config['mode']);
        $asyncDefinition->setArgument('$prefix', $config['crud_prefix']);
        $asyncDefinition->setArgument('$actionsConfig', $config['actions']);

        $articlesDefinition = $container->getDefinition('xenolab_xeno_engine.article_controller');
        $articlesDefinition->setArgument('$authors', $config['author_providers']['articles']);

        $pagesDefinition = $container->getDefinition('xenolab_xeno_engine.page_controller');
        $pagesDefinition->setArgument('$authors', $config['author_providers']['pages']);
    }
}
