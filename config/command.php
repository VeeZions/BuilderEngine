<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use VeeZions\BuilderEngine\Command\BuilderImportTemplatesCommand;
use VeeZions\BuilderEngine\Command\BuilderLibrariesReferencesCommand;
use VeeZions\BuilderEngine\Command\BuilderDeployBlogCommand;
use VeeZions\BuilderEngine\Command\BuilderRemoveResourcesCommand;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\abstract_arg;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services
        ->set('veezions_builder_engine.command.import_templates', BuilderImportTemplatesCommand::class)
        ->args([
            service('parameter_bag'),
            abstract_arg('Get config.mode value'),
        ])
        ->tag('console.command')
    ;

    $services
        ->set('veezions_builder_engine.command.deploy_blog', BuilderDeployBlogCommand::class)
        ->args([
            service('parameter_bag'),
        ])
        ->tag('console.command')
    ;

    $services
        ->set('veezions_builder_engine.command.remove_resources', BuilderRemoveResourcesCommand::class)
        ->args([
            service('parameter_bag'),
        ])
        ->tag('console.command')
    ;

    $services
        ->set('veezions_builder_engine.command.libraries.references', BuilderLibrariesReferencesCommand::class)
        ->tag('console.command')
    ;
};
