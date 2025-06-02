<?php

use VeeZions\BuilderEngine\Controller\CategoryController;
use VeeZions\BuilderEngine\Controller\ArticleController;
use VeeZions\BuilderEngine\Controller\NavigationController;
use VeeZions\BuilderEngine\Controller\LibraryController;
use VeeZions\BuilderEngine\Controller\PageController;
use VeeZions\BuilderEngine\Controller\AsyncController;
use VeeZions\BuilderEngine\Controller\FrontController;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\abstract_arg;

return static function (ContainerConfigurator $container) {

    $services = $container->services();

    $services
        ->set('veezions_builder_engine.async_controller', AsyncController::class)
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
        ->set('veezions_builder_engine.category_controller', CategoryController::class)
        ->args([
            service('veezions_builder_engine.engine_manager')
        ])
        ->tag('controller.service_arguments')
    ;

    $services
        ->set('veezions_builder_engine.article_controller', ArticleController::class)
        ->args([
            service('veezions_builder_engine.engine_manager')
        ])
        ->tag('controller.service_arguments')
    ;

    $services
        ->set('veezions_builder_engine.page_controller', PageController::class)
        ->args([
            service('veezions_builder_engine.engine_manager')
        ])
        ->tag('controller.service_arguments')
    ;

    $services
        ->set('veezions_builder_engine.navigation_controller', NavigationController::class)
        ->args([
            service('veezions_builder_engine.engine_manager')
        ])
        ->tag('controller.service_arguments')
    ;

    $services
        ->set('veezions_builder_engine.library_controller', LibraryController::class)
        ->args([
            service('doctrine.orm.entity_manager'),
            service('veezions_builder_engine.engine_manager'),
            service('veezions_builder_engine.asset_manager')
        ])
        ->tag('controller.service_arguments')
    ;

    $services
        ->set('veezions_builder_engine.front_controller', FrontController::class)
        ->args([
            service('twig'),
            service('router'),
            service('doctrine.orm.entity_manager'),
        ])
        ->tag('controller.service_arguments')
    ;
};
