<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Vision\BuilderEngine\Command\BuilderImportTemplatesCommand;
use Vision\BuilderEngine\Command\BuilderLibrariesReferencesCommand;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\abstract_arg;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services
        ->set('vision_builder_engine.command.import_templates', BuilderImportTemplatesCommand::class)
        ->args([
            service('parameter_bag'),
            abstract_arg('Get config.mode value'),
        ])
        ->tag('console.command')
    ;

    $services
        ->set('vision_builder_engine.command.libraries.references', BuilderLibrariesReferencesCommand::class)
        ->tag('console.command')
    ;
};
