<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use VeeZions\BuilderEngine\Twig\Runtime\FiltersRuntime;
use VeeZions\BuilderEngine\Twig\Extension\FiltersExtension;
use VeeZions\BuilderEngine\Twig\GlobalVariables;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->services()

        ->set('veezions_builder_engine.twig.extension', FiltersExtension::class)
        ->tag('twig.extension')

        ->set('veezions_builder_engine.twig.global', GlobalVariables::class)

        ->set('veezions_builder_engine.twig.page_runtime', FiltersRuntime::class)
        ->tag('twig.runtime')
    ;
};
