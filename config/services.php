<?php

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
            service('router'),
            service('translator'),
            service('doctrine.orm.entity_manager'),
            service('security.authorization_checker'),
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
        ->set(XenoArticleRepository::class, XenoArticleRepository::class)
        ->args([
            service('doctrine'),
        ])
        ->tag('doctrine.repository_service')
    ;

    $services
        ->set(XenoPageRepository::class, XenoPageRepository::class)
        ->args([
            service('doctrine'),
        ])
        ->tag('doctrine.repository_service')
    ;

    $services
        ->set(XenoNavigationRepository::class, XenoNavigationRepository::class)
        ->args([
            service('doctrine'),
        ])
        ->tag('doctrine.repository_service')
    ;

    $services
        ->set(XenoLibraryRepository::class, XenoLibraryRepository::class)
        ->args([
            service('doctrine'),
        ])
        ->tag('doctrine.repository_service')
    ;
};
