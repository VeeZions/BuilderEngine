<?php

namespace VeeZions\BuilderEngine\DependencyInjection;

use Behat\Gherkin\Loader\YamlFileLoader;
use League\Flysystem\Filesystem as OneupFlysystem;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\Filesystem\Exception\InvalidArgumentException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Yaml\Yaml;
use VeeZions\BuilderEngine\Constant\AssetConstant;
use VeeZions\BuilderEngine\Constant\ConfigConstant;
use VeeZions\BuilderEngine\Repository\BuilderPageRepository;
use VeeZions\BuilderEngine\Repository\BuilderArticleRepository;
use VeeZions\BuilderEngine\Provider\PackageConfigProvider;

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

        $this->setDefinitions($config, $container);
    }

    public function getConfiguration(array $config, ContainerBuilder $container): BuilderEngineConfiguration
    {
        return new BuilderEngineConfiguration();
    }

    private function setDefinitions(array $config, ContainerBuilder $container): void
    {
        $this->setRouteLoaderDefinition($config, $container);
        $this->setCommandsDefinition($config, $container);
        $this->setformManagerDefinition($config, $container);
        $this->setEngineManagerDefinition($config, $container);
        $this->setAssetConstantDefinition($config, $container);
        $this->setGlobalVariableDefinition($config, $container);
        $this->setPageRuntimeDefinition($config, $container);
        $this->setHtmlManagerDefinition($config, $container);
        $this->setTableConstantDefinition($config, $container);
        $this->setArticleRepositoryDefinition($config, $container);
        $this->setPageRepositoryDefinition($config, $container);
        $this->setAssetManagerDefinition($config, $container);
    }

    private function setRouteLoaderDefinition(array $config, ContainerBuilder $container): void
    {
        $def = $container->getDefinition('veezions_builder_engine.route_loader');
        $def->setArgument('$mode', $config['mode']);
        $def->setArgument('$prefix', $config['crud_prefix']);
        $def->setArgument('$actionsConfig', $config['actions']);
    }

    private function setCommandsDefinition(array $config, ContainerBuilder $container): void
    {
        $def = $container->getDefinition('veezions_builder_engine.command.import_templates');
        $def->setArgument('$mode', $config['mode']);
    }

    private function setformManagerDefinition(array $config, ContainerBuilder $container): void
    {
        $def = $container->getDefinition('veezions_builder_engine.form_manager');
        $def->setArgument('$authors', $config['author_providers']);
        $def->setArgument('$bundleMode', $config['mode']);
        $def->setArgument('$libraryLiipFilters', $config['library_config']['liip_filter_sets']);
        $def->setArgument('$customRoutes', $config['custom_routes']);
        $def->setArgument('$actions', $config['actions']);
        $def->setArgument('$formTheme', $config['form_theme']);
        $def->setArgument('$localeFallback', $config['locale_fallback']);
    }

    private function setEngineManagerDefinition(array $config, ContainerBuilder $container): void
    {
        $def = $container->getDefinition('veezions_builder_engine.engine_manager');
        $def->setArgument('$authors', $config['author_providers']);
        $def->setArgument('$libraryLiipFilters', $config['library_config']['liip_filter_sets']);
        $def->setArgument('$customRoutes', $config['custom_routes']);
        $def->setArgument('$actions', $config['actions']);
        $def->setArgument('$formTheme', $config['form_theme']);
        $def->setArgument('$localeFallback', $config['locale_fallback']);
    }

    private function setAssetConstantDefinition(array $config, ContainerBuilder $container): void
    {
        $def = $container->getDefinition('veezions_builder_engine.asset_constant');
        $def->setArgument(
            '$liipFilters',
            $container->hasParameter('liip_imagine.filter_sets')
                ? $container->getParameter('liip_imagine.filter_sets')
                : []
        );
    }

    private function setGlobalVariableDefinition(array $config, ContainerBuilder $container): void
    {
        $def = $container->getDefinition('veezions_builder_engine.twig.global');
        $def->setArgument('$extended_template', $config['extended_template']);
        $def->setArgument('$form_theme', $config['form_theme']);
        $def->setArgument('$customRoutes', $config['custom_routes']);
        $def->setArgument('$pagination_templates', $config['pagination_buttons']);
        $def->setArgument('$crud_buttons', $config['crud_buttons']);
        $def->setArgument('$internal_css', $config['internal_css']);
        $def->setArgument('$page_title', $config['page_title_display']);
    }

    private function setPageRuntimeDefinition(array $config, ContainerBuilder $container): void
    {
        $def = $container->getDefinition('veezions_builder_engine.twig.page_runtime');
        $def->setArgument('$customRoutes', $config['custom_routes']);
        $def->setArgument('$page_title', $config['page_title_display']);
    }

    private function setHtmlManagerDefinition(array $config, ContainerBuilder $container): void
    {
        $def = $container->getDefinition('veezions_builder_engine.html_manager');
        $def->setArgument('$customRoutes', $config['custom_routes']);
        $def->setArgument('$actions', $config['actions']);
    }

    private function setTableConstantDefinition(array $config, ContainerBuilder $container): void
    {
        $def = $container->getDefinition('veezions_builder_engine.table_constant');
        $def->setArgument('$authors', $config['author_providers']);
    }

    private function setArticleRepositoryDefinition(array $config, ContainerBuilder $container): void
    {
        $def = $container->getDefinition(BuilderArticleRepository::class);
        $def->setArgument('$authors', $config['author_providers']['articles']);
    }

    private function setPageRepositoryDefinition(array $config, ContainerBuilder $container): void
    {
        $def = $container->getDefinition(BuilderPageRepository::class);
        $def->setArgument('$authors', $config['author_providers']['articles']);
    }

    private function setAssetManagerDefinition(array $config, ContainerBuilder $container): void
    {
        $def = $container->getDefinition('veezions_builder_engine.asset_manager');
        $def->setArgument('$libraryConfig', $config['library_config']);
    }
}
