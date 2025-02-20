<?php

use XenoLab\XenoEngine\DataCollector\XenoCollector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {

    $services = $container->services();

    $services
        ->set('xenolab_xeno_engine.data_collector', XenoCollector::class)
        ->tag('data_collector', [
            'template' => '@XenoEngineAsynchronousInternal/profiler/navigation.html.twig',
            'id' => 'xenolab_collector'
        ])
    ;
};
