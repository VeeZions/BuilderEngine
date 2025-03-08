<?php

use VeeZions\BuilderEngine\Repository\BuilderCategoryRepository;
use VeeZions\BuilderEngine\Repository\BuilderArticleRepository;
use VeeZions\BuilderEngine\Repository\BuilderElementRepository;
use VeeZions\BuilderEngine\Repository\BuilderPageRepository;
use VeeZions\BuilderEngine\Repository\BuilderNavigationRepository;
use VeeZions\BuilderEngine\Repository\BuilderLibraryRepository;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Knp\Component\Pager\PaginatorInterface;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\abstract_arg;

return static function (ContainerConfigurator $container) {

    $services = $container->services();

    $services
        ->set(BuilderCategoryRepository::class, BuilderCategoryRepository::class)
        ->args([
            service('doctrine'),
            service(PaginatorInterface::class),
        ])
        ->tag('doctrine.repository_service')
    ;

    $services
        ->set(BuilderArticleRepository::class, BuilderArticleRepository::class)
        ->args([
            service('doctrine'),
            service(PaginatorInterface::class),
        ])
        ->tag('doctrine.repository_service')
    ;

    $services
        ->set(BuilderPageRepository::class, BuilderPageRepository::class)
        ->args([
            service('doctrine'),
            service(PaginatorInterface::class),
        ])
        ->tag('doctrine.repository_service')
    ;

    $services
        ->set(BuilderNavigationRepository::class, BuilderNavigationRepository::class)
        ->args([
            service('doctrine'),
            service(PaginatorInterface::class),
        ])
        ->tag('doctrine.repository_service')
    ;

    $services
        ->set(BuilderLibraryRepository::class, BuilderLibraryRepository::class)
        ->args([
            service('doctrine'),
            service(PaginatorInterface::class),
        ])
        ->tag('doctrine.repository_service')
    ;

    $services
        ->set(BuilderElementRepository::class, BuilderElementRepository::class)
        ->args([
            service('doctrine'),
        ])
        ->tag('doctrine.repository_service')
    ;
};
