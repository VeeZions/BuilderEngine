<?php

use XenoLab\XenoEngine\DataCollector\XenoCollector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use function Symfony\Component\DependencyInjection\Loader\Configurator\abstract_arg;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_locator;

return static function (ContainerConfigurator $container): void {

    $services = $container->services();

    $services
        ->set('xenolab_xeno_engine.data_collector', XenoCollector::class)
        ->tag('data_collector', [
            'template' => '@XenoEngine/profiler/navigation.html.twig',
            'id' => 'xenolab_collector'
        ])
    ;
};
