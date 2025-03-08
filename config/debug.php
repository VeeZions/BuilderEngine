<?php

use Vision\BuilderEngine\DataCollector\BuilderCollector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {

    $services = $container->services();

    $services
        ->set('vision_builder_engine.data_collector', BuilderCollector::class)
        ->tag('data_collector', [
            'template' => '@BuilderEngineInternal/profiler/navigation.html.twig',
            'id' => 'vision_collector'
        ])
    ;
};
