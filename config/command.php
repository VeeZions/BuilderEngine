<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use XenoLab\XenoEngine\Command\XenoImportTemplatesCommand;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services
        ->set('xenolab_xeno_engine.command.import_templates', XenoImportTemplatesCommand::class)
        ->tag('console.command')
    ;
};
