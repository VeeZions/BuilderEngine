<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use XenoLab\XenoEngine\Command\XenoImportTemplatesCommand;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services
        ->set('xenolab_xeno_engine.command.import_templates', XenoImportTemplatesCommand::class)
        ->args([
            service('parameter_bag'),
        ])
        ->tag('console.command')
    ;
};
