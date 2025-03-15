<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use VeeZions\BuilderEngine\Twig\Runtime\FiltersRuntime;
use VeeZions\BuilderEngine\Twig\Extension\FiltersExtension;
use VeeZions\BuilderEngine\Twig\GlobalVariables;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\abstract_arg;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services
        ->set('veezions_builder_engine.twig.extension', FiltersExtension::class)
        ->tag('twig.extension')
    ;

    $services
        ->set('veezions_builder_engine.twig.global', GlobalVariables::class)
        ->args([
            service('twig'),
            abstract_arg('Get config.extended_template value'),
            abstract_arg('Get config.form_theme value'),
            abstract_arg('Get config.custom_routes'),
            abstract_arg('Get config.pagination_buttons'),
            abstract_arg('Get config.crud_buttons'),
        ])
        ->tag('twig.global')
    ;

    $services
        ->set('veezions_builder_engine.twig.page_runtime', FiltersRuntime::class)
        ->args([
            service('veezions_builder_engine.html_manager'),
            service('request_stack'),
            service('veezions_builder_engine.form_manager'),
            abstract_arg('Get config.custom_routes'),
        ])
        ->tag('twig.runtime')
    ;
};
