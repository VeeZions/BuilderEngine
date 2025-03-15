<?php

namespace VeeZions\BuilderEngine\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use VeeZions\BuilderEngine\Constant\ConfigConstant;
use VeeZions\BuilderEngine\Repository\BuilderPageRepository;
use VeeZions\BuilderEngine\Repository\BuilderArticleRepository;

class BuilderEngineExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new PhpFileLoader(
            $container,
            new FileLocator(__DIR__.'/../../config')
        );

        $loader->load('repositories.php');
        $loader->load('services.php');

        if (ConfigConstant::CONFIG_MODE_FORM !== $config['mode']) {
            $loader->load('controllers.php');
        }

        if (true === $container->getParameter('kernel.debug')) {
            $loader->load('debug.php');
        }

        $loader->load('twig.php');
        $loader->load('command.php');

        $this->setControllersArgumentsFromConfig($config, $container);
    }

    public function getConfiguration(array $config, ContainerBuilder $container): BuilderEngineConfiguration
    {
        return new BuilderEngineConfiguration();
    }

    private function setControllersArgumentsFromConfig(array $config, ContainerBuilder $container): void
    {
        $liipFilters = $container->hasParameter('liip_imagine.filter_sets') 
            ? $container->getParameter('liip_imagine.filter_sets') 
            : [];
        
        $routeLoaderDefinition = $container->getDefinition('veezions_builder_engine.route_loader');
        $routeLoaderDefinition->setArgument('$mode', $config['mode']);
        $routeLoaderDefinition->setArgument('$prefix', $config['crud_prefix']);
        $routeLoaderDefinition->setArgument('$actionsConfig', $config['actions']);

        $importCommandDefinition = $container->getDefinition('veezions_builder_engine.command.import_templates');
        $importCommandDefinition->setArgument('$mode', $config['mode']);
        
        $formManagerDefinition = $container->getDefinition('veezions_builder_engine.form_manager');
        $formManagerDefinition->setArgument('$authors', $config['author_providers']);
        $formManagerDefinition->setArgument('$bundleMode', $config['mode']);
        $formManagerDefinition->setArgument('$libraryLiipFilters', $config['library_config']['liip_filter_sets']);
        $formManagerDefinition->setArgument('$customRoutes', $config['custom_routes']);
        $formManagerDefinition->setArgument('$actions', $config['actions']);

        $engineManagerDefinition = $container->getDefinition('veezions_builder_engine.engine_manager');
        $engineManagerDefinition->setArgument('$authors', $config['author_providers']);
        $engineManagerDefinition->setArgument('$customRoutes', $config['custom_routes']);
        $engineManagerDefinition->setArgument('$actions', $config['actions']);
        
        $assetConstantDefinition = $container->getDefinition('veezions_builder_engine.asset_constant');
        $assetConstantDefinition->setArgument('$liipFilters', $liipFilters);
        
        $globalVariableDefinition = $container->getDefinition('veezions_builder_engine.twig.global');
        $globalVariableDefinition->setArgument('$extended_template', $config['extended_template']);
        $globalVariableDefinition->setArgument('$form_theme', $config['form_theme']);
        $globalVariableDefinition->setArgument('$customRoutes', $config['custom_routes']);
        $globalVariableDefinition->setArgument('$pagination_templates', $config['pagination_buttons']);
        $globalVariableDefinition->setArgument('$crud_buttons', $config['crud_buttons']);
        
        $pageRuntimeDefinition = $container->getDefinition('veezions_builder_engine.twig.page_runtime');
        $pageRuntimeDefinition->setArgument('$customRoutes', $config['custom_routes']);

        $htmlManagerDefinition = $container->getDefinition('veezions_builder_engine.html_manager');
        $htmlManagerDefinition->setArgument('$customRoutes', $config['custom_routes']);
        $htmlManagerDefinition->setArgument('$actions', $config['actions']);

        $tableConstantDefinition = $container->getDefinition('veezions_builder_engine.table_constant');
        $tableConstantDefinition->setArgument('$authors', $config['author_providers']);

        $articleRepositoryDefinition = $container->getDefinition(BuilderArticleRepository::class);
        $articleRepositoryDefinition->setArgument('$authors', $config['author_providers']['articles']);

        $pageRepositoryDefinition = $container->getDefinition(BuilderPageRepository::class);
        $pageRepositoryDefinition->setArgument('$authors', $config['author_providers']['articles']);
    }
}
