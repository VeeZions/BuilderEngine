<?php

use XenoLab\XenoEngine\Controller\CategoryController;
use XenoLab\XenoEngine\Controller\ArticleController;
use XenoLab\XenoEngine\Controller\NavigationController;
use XenoLab\XenoEngine\Controller\LibraryController;
use XenoLab\XenoEngine\Controller\PageController;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\abstract_arg;

return static function (ContainerConfigurator $container) {

    $services = $container->services();

    $services
        ->set('xenolab_xeno_engine.category_controller', CategoryController::class)
        ->args([
            service('twig'),
            service('router'),
            service('translator'),
            service('doctrine.orm.entity_manager'),
            service('security.authorization_checker'),
            abstract_arg('Get config.actions'),
        ])
        ->tag('controller.service_arguments')
    ;

    $services
        ->set('xenolab_xeno_engine.article_controller', ArticleController::class)
        ->args([
            service('twig'),
            service('router'),
            service('translator'),
            service('doctrine.orm.entity_manager'),
            service('security.authorization_checker'),
            abstract_arg('Articles authors provider'),
            abstract_arg('Get config.actions'),
        ])
        ->tag('controller.service_arguments')
    ;

    $services
        ->set('xenolab_xeno_engine.page_controller', PageController::class)
        ->args([
            service('twig'),
            service('router'),
            service('translator'),
            service('doctrine.orm.entity_manager'),
            service('security.authorization_checker'),
            abstract_arg('Pages authors provider'),
            abstract_arg('Get config.actions'),
        ])
        ->tag('controller.service_arguments')
    ;

    $services
        ->set('xenolab_xeno_engine.navigation_controller', NavigationController::class)
        ->args([
            service('twig'),
            service('router'),
            service('translator'),
            service('doctrine.orm.entity_manager'),
            service('security.authorization_checker'),
            abstract_arg('Get config.actions'),
        ])
        ->tag('controller.service_arguments')
    ;

    $services
        ->set('xenolab_xeno_engine.library_controller', LibraryController::class)
        ->args([
            service('twig'),
            service('router'),
            service('translator'),
            service('doctrine.orm.entity_manager'),
            service('security.authorization_checker'),
            abstract_arg('Get config.actions'),
        ])
        ->tag('controller.service_arguments')
    ;
};
