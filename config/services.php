<?php

use VeeZions\BuilderEngine\Loader\BuilderEngineLoader;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use VeeZions\BuilderEngine\Manager\AssetManager;
use VeeZions\BuilderEngine\Manager\CmsManager;
use VeeZions\BuilderEngine\Manager\FormManager;
use VeeZions\BuilderEngine\Manager\GedManager;
use VeeZions\BuilderEngine\Manager\NavigationManager;
use VeeZions\BuilderEngine\Constant\AssetConstant;
use VeeZions\BuilderEngine\Constant\Crud\ArticleConstant;
use VeeZions\BuilderEngine\Constant\Crud\CategoryConstant;
use VeeZions\BuilderEngine\Constant\Crud\NavigationConstant;
use VeeZions\BuilderEngine\Constant\Crud\PageConstant;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\abstract_arg;

return static function (ContainerConfigurator $container) {

    $services = $container->services();

    $services
        ->set('veezions_builder_engine.route_loader', BuilderEngineLoader::class)
        ->args([
            abstract_arg('Get config.mode value'),
            abstract_arg('Get config.crud_prefix value'),
            abstract_arg('Get config.actions'),
        ])
        ->tag('routing.loader')
    ;

    $services->set('veezions_builder_engine.asset_constant', AssetConstant::class);

    $services
        ->set('veezions_builder_engine.form_manager', FormManager::class)
        ->args([
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
        ])
    ;
    
    $services
        ->set('veezions_builder_engine.asset_manager', AssetManager::class)
        ->args([
            service('oneup_flysystem.default_filesystem_filesystem'),
            service('liip_imagine.cache.manager'),
            service('parameter_bag'),
            service('veezions_builder_engine.asset_constant'),
            service('liip_imagine.filter.manager'),
            service('liip_imagine.data.manager'),
            service('doctrine.orm.entity_manager'),
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
        ->set('veezions_builder_engine.article_constant', ArticleConstant::class)
        ->args([
            service('translator'),
        ])
    ;

    $services
        ->set('veezions_builder_engine.category_constant', CategoryConstant::class)
    ;

    $services
        ->set('veezions_builder_engine.navigation_constant', NavigationConstant::class)
    ;

    $services
        ->set('veezions_builder_engine.page_constant', PageConstant::class)
        ->args([
            service('translator'),
        ])
    ;

    $services
        ->set('veezions_builder_engine.asset_constant', AssetConstant::class)
        ->args([
            abstract_arg('LiipImagineBundle filters'),
        ])
    ;
};
