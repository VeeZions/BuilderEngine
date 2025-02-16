<?php

use XenoLabs\XenoEngine\Controller\EngineController;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container) {

    $services = $container->services();
    
    $services->set('xenolabs_xeno_engine.engine_controller', EngineController::class)
        ->args([
            service('twig'),
        ])
        ->tag('controller.service_arguments')
    ;
};
