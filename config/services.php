<?php

use VeeZions\BuilderEngine\Loader\BuilderEngineLoader;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use VeeZions\BuilderEngine\Manager\AssetManager;
use VeeZions\BuilderEngine\Manager\CmsManager;
use VeeZions\BuilderEngine\Manager\FormManager;
use VeeZions\BuilderEngine\Manager\GedManager;
use VeeZions\BuilderEngine\Manager\NavigationManager;
use VeeZions\BuilderEngine\Constant\AssetConstant;
use VeeZions\BuilderEngine\Constant\NavigationConstant;
use VeeZions\BuilderEngine\Manager\HtmlManager;
use VeeZions\BuilderEngine\Constant\TableConstant;
use VeeZions\BuilderEngine\Manager\EngineManager;
use VeeZions\BuilderEngine\Provider\AuthorProvider;
use VeeZions\BuilderEngine\Provider\LocaleProvider;
use VeeZions\BuilderEngine\Form\Type\LocaleType;
use VeeZions\BuilderEngine\Form\Type\ButtonsType;
use VeeZions\BuilderEngine\Manager\CategoriesManager;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\abstract_arg;

return static function (ContainerConfigurator $container) {

    $services = $container->services();

    $services
        ->set('veezions_builder_engine.route_loader', BuilderEngineLoader::class)
        ->args([
            abstract_arg('Get config.mode value'),
            abstract_arg('Get config.crud_prefix value'),
            abstract_arg('Frontend blog, article and category routes'),
            abstract_arg('Get config.actions'),
        ])
        ->tag('routing.loader')
    ;

    $services->set('veezions_builder_engine.asset_constant', AssetConstant::class);

    $services
        ->set('veezions_builder_engine.form_manager', FormManager::class)
        ->args([
            service('twig'),
            service('form.factory'),
            service('request_stack'),
            service('doctrine.orm.entity_manager'),
            service('security.token_storage'),
            service('translator'),
            service('router'),
            service('veezions_builder_engine.ged_manager'),
            service('veezions_builder_engine.cms_manager'),
            service('veezions_builder_engine.asset_manager'),
            abstract_arg('Authors provider'),
            abstract_arg('Bundle mode'),
            abstract_arg('Get liipImagine filter sets'),
            abstract_arg('Get config.custom_routes'),
            service('veezions_builder_engine.table_constant'),
            service('security.authorization_checker'),
            abstract_arg('Get config.actions'),
            service('veezions_builder_engine.locale_provider'),
            service('veezions_builder_engine.author_provider'),
            abstract_arg('Get config.form_theme'),
            service('veezions_builder_engine.navigation_constant'),
            abstract_arg('Get config.locale_fallback'),
            abstract_arg('Get config.library_config.max_upload_file'),
            abstract_arg('Frontend blog, article and category routes'),
            abstract_arg('Crud pagination limit'),
            abstract_arg('Front pagination limit'),
            service('veezions_builder_engine.categories_manager')
        ])
    ;
    
    $services
        ->set('veezions_builder_engine.asset_manager', AssetManager::class)
        ->args([
            service('oneup_flysystem.vbe_uploads_filesystem'),
            service('liip_imagine.cache.manager'),
            service('parameter_bag'),
            service('veezions_builder_engine.asset_constant'),
            service('liip_imagine.filter.manager'),
            service('liip_imagine.data.manager'),
            service('doctrine.orm.entity_manager'),
            abstract_arg('Get config.library_config'),
        ])
    ;
    
    $services
        ->set('veezions_builder_engine.cms_manager', CmsManager::class)
        ->args([
            service('parameter_bag'),
            service('filesystem'),
            service('doctrine.orm.entity_manager'),
        ])
    ;
    
    $services
        ->set('veezions_builder_engine.ged_manager', GedManager::class)
        ->args([
            service('doctrine.orm.entity_manager'),
            service('translator'),
        ])
    ;
    
    $services
        ->set('veezions_builder_engine.navigation_manager', NavigationManager::class)
        ->args([
            service('doctrine.orm.entity_manager'),
        ])
    ;

    $services
        ->set('veezions_builder_engine.html_manager', HtmlManager::class)
        ->args([
            service('twig'),
            service('translator'),
            service('request_stack'),
            service('veezions_builder_engine.table_constant'),
            service('security.authorization_checker'),
            service('router'),
            abstract_arg('Get config.custom_routes'),
            abstract_arg('Get config.actions'),
        ])
    ;

    $services
        ->set('veezions_builder_engine.asset_constant', AssetConstant::class)
        ->args([
            abstract_arg('LiipImagineBundle filters'),
        ])
    ;

    $services
        ->set('veezions_builder_engine.table_constant', TableConstant::class)
        ->args([
            service('veezions_builder_engine.author_provider'),
            abstract_arg('Authors provider'),
        ])
    ;

    $services->set('veezions_builder_engine.navigation_constant', NavigationConstant::class);

    $services
        ->set('veezions_builder_engine.engine_manager', EngineManager::class)
        ->args([
            service('twig'),
            service('form.factory'),
            service('request_stack'),
            service('doctrine.orm.entity_manager'),
            service('security.token_storage'),
            service('translator'),
            service('router'),
            service('veezions_builder_engine.asset_manager'),
            abstract_arg('Authors provider'),
            abstract_arg('Get liipImagine filter sets'),
            abstract_arg('Get config.custom_routes'),
            service('veezions_builder_engine.table_constant'),
            service('security.authorization_checker'),
            abstract_arg('Get config.actions'),
            service('veezions_builder_engine.locale_provider'),
            service('veezions_builder_engine.author_provider'),
            abstract_arg('Get config.form_theme'),
            service('veezions_builder_engine.navigation_constant'),
            abstract_arg('Get config.locale_fallback'),
            abstract_arg('Get config.library_config.max_upload_file'),
            abstract_arg('Frontend blog, article and category routes'),
            abstract_arg('Crud pagination limit'),
            abstract_arg('Front pagination limit'),
            service('veezions_builder_engine.categories_manager')
        ])
    ;

    $services
        ->set('veezions_builder_engine.author_provider', AuthorProvider::class)
        ->args([
            service('doctrine.orm.entity_manager'),
        ])
    ;

    $services
        ->set('veezions_builder_engine.locale_provider', LocaleProvider::class)
        ->args([
            abstract_arg('Get config.enabled_locales'),
        ])
    ;

    $services
        ->set('veezions_builder_engine.categories_manager', CategoriesManager::class)
        ->args([
            service('doctrine.orm.entity_manager'),
            service('request_stack'),
        ])
    ;
};
