<?php

use XenoLab\XenoEngine\Controller\CategoryController;
use XenoLab\XenoEngine\Controller\ArticleController;
use XenoLab\XenoEngine\Controller\NavigationController;
use XenoLab\XenoEngine\Controller\LibraryController;
use XenoLab\XenoEngine\Controller\PageController;
use XenoLab\XenoEngine\Controller\AsyncController;
use XenoLab\XenoEngine\Loader\XenoEngineLoader;
use XenoLab\XenoEngine\Repository\XenoCategoryRepository;
use XenoLab\XenoEngine\Repository\XenoArticleRepository;
use XenoLab\XenoEngine\Repository\XenoPageRepository;
use XenoLab\XenoEngine\Repository\XenoNavigationRepository;
use XenoLab\XenoEngine\Repository\XenoLibraryRepository;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\abstract_arg;

return static function (ContainerConfigurator $container) {

    $services = $container->services();

    $services
        ->set('xenolab_xeno_engine.route_loader', XenoEngineLoader::class)
        ->args([
            abstract_arg('Get config.mode value'),
            abstract_arg('Get config.crud_prefix value'),
            abstract_arg('Get config.actions'),
        ])
        ->tag('routing.loader')
    ;
    
    $services
        ->set('xenolab_xeno_engine.async_controller', AsyncController::class)
        ->args([
            service('twig'),
        ])
        ->tag('controller.service_arguments')
    ;

    $services
        ->set(XenoCategoryRepository::class, XenoCategoryRepository::class)
        ->args([
            service('doctrine'),
        ])
        ->tag('doctrine.repository_service')
    ;
    
    $services
        ->set('xenolab_xeno_engine.category_controller', CategoryController::class)
        ->args([
            service('twig'),
        ])
        ->tag('controller.service_arguments')
    ;

    $services
        ->set(XenoArticleRepository::class, XenoArticleRepository::class)
        ->args([
            service('doctrine'),
        ])
        ->tag('doctrine.repository_service')
    ;
    
    $services
        ->set('xenolab_xeno_engine.article_controller', ArticleController::class)
        ->args([
            service('twig'),
            abstract_arg('Articles authors provider'),
        ])
        ->tag('controller.service_arguments')
    ;

    $services
        ->set(XenoPageRepository::class, XenoPageRepository::class)
        ->args([
            service('doctrine'),
        ])
        ->tag('doctrine.repository_service')
    ;
    
    $services
        ->set('xenolab_xeno_engine.page_controller', PageController::class)
        ->args([
            service('twig'),
            abstract_arg('Pages authors provider'),
        ])
        ->tag('controller.service_arguments')
    ;

    $services
        ->set(XenoNavigationRepository::class, XenoNavigationRepository::class)
        ->args([
            service('doctrine'),
        ])
        ->tag('doctrine.repository_service')
    ;
    
    $services
        ->set('xenolab_xeno_engine.navigation_controller', NavigationController::class)
        ->args([
            service('twig'),
        ])
        ->tag('controller.service_arguments')
    ;

    $services
        ->set(XenoLibraryRepository::class, XenoLibraryRepository::class)
        ->args([
            service('doctrine'),
        ])
        ->tag('doctrine.repository_service')
    ;
    
    $services
        ->set('xenolab_xeno_engine.library_controller', LibraryController::class)
        ->args([
            service('twig'),
        ])
        ->tag('controller.service_arguments')
    ;
};
