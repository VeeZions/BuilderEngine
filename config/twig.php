<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use XenoLab\XenoEngine\Twig\Runtime\FiltersRuntime;
use XenoLab\XenoEngine\Twig\Extension\FiltersExtension;
use XenoLab\XenoEngine\Twig\GlobalVariables;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->services()

        ->set('xenolab_xeno_engine.twig.extension', FiltersExtension::class)
        ->tag('twig.extension')

        ->set('xenolab_xeno_engine.twig.global', GlobalVariables::class)

        ->set('xenolab_xeno_engine.twig.page_runtime', FiltersRuntime::class)
        ->tag('twig.runtime')
    ;
};
