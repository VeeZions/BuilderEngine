<?php

namespace XenoLab\XenoEngine\DependencyInjection;

use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\Reference;

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

        $loader->load('twig.php');
        $loader->load('command.php');

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
        $articlesDefinition->setArgument('$actions', $config['actions']['articles']);

        $pagesDefinition = $container->getDefinition('xenolab_xeno_engine.page_controller');
        $pagesDefinition->setArgument('$authors', $config['author_providers']['pages']);
        $pagesDefinition->setArgument('$actions', $config['actions']['pages']);

        $categoriesDefinition = $container->getDefinition('xenolab_xeno_engine.category_controller');
        $categoriesDefinition->setArgument('$actions', $config['actions']['categories']);

        $navigationsDefinition = $container->getDefinition('xenolab_xeno_engine.navigation_controller');
        $navigationsDefinition->setArgument('$actions', $config['actions']['navigations']);

        $librariesDefinition = $container->getDefinition('xenolab_xeno_engine.library_controller');
        $librariesDefinition->setArgument('$actions', $config['actions']['libraries']);
    }
}
