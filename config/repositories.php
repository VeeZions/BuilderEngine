<?php

use Vision\BuilderEngine\Repository\BuilderCategoryRepository;
use Vision\BuilderEngine\Repository\BuilderArticleRepository;
use Vision\BuilderEngine\Repository\BuilderElementRepository;
use Vision\BuilderEngine\Repository\BuilderPageRepository;
use Vision\BuilderEngine\Repository\BuilderNavigationRepository;
use Vision\BuilderEngine\Repository\BuilderLibraryRepository;
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
