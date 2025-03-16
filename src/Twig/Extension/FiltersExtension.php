<?php

namespace VeeZions\BuilderEngine\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use VeeZions\BuilderEngine\Twig\Runtime\FiltersRuntime;

final class FiltersExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('vbe_value_mutator', [FiltersRuntime::class, 'valueMutator']),
            new TwigFilter('vbe_is_sortable', [FiltersRuntime::class, 'isSortable']),
            new TwigFilter('vbe_remove_alias', [FiltersRuntime::class, 'removeAlias']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('vbe_container_id', [FiltersRuntime::class, 'containerId']),
            new TwigFunction('vbe_build_table', [FiltersRuntime::class, 'buildTable']),
            new TwigFunction('vbe_build_filters', [FiltersRuntime::class, 'buildFilters']),
            new TwigFunction('vbe_build_pagination', [FiltersRuntime::class, 'buildPagination']),
            new TwigFunction('vbe_build_counter', [FiltersRuntime::class, 'buildCounter']),
            new TwigFunction('vbe_build_create', [FiltersRuntime::class, 'buildCreate']),
            new TwigFunction('vbe_build_render', [FiltersRuntime::class, 'buildRender']),
            new TwigFunction('vbe_title', [FiltersRuntime::class, 'getPageTitle']),
            new TwigFunction('vbe_flags', [FiltersRuntime::class, 'getFlags']),
        ];
    }
}
