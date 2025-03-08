<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Vision\BuilderEngine\Twig\Runtime\FiltersRuntime;
use Vision\BuilderEngine\Twig\Extension\FiltersExtension;
use Vision\BuilderEngine\Twig\GlobalVariables;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->services()

        ->set('vision_builder_engine.twig.extension', FiltersExtension::class)
        ->tag('twig.extension')

        ->set('vision_builder_engine.twig.global', GlobalVariables::class)

        ->set('vision_builder_engine.twig.page_runtime', FiltersRuntime::class)
        ->tag('twig.runtime')
    ;
};
