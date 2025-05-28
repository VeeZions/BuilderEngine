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
            new TwigFilter('vbe_file_extension', [FiltersRuntime::class, 'fileExtension']),
            new TwigFilter('vbe_humanize_size', [FiltersRuntime::class, 'humanizeSize']),
            new TwigFilter('vbe_get_all_elements_from_page', [FiltersRuntime::class, 'getAllElementsFromPage']),
            new TwigFilter('vbe_get_real_elements_length', [FiltersRuntime::class, 'getRealElementsLength']),
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
            new TwigFunction('vbe_aws_s3', [FiltersRuntime::class, 'awsS3']),
            new TwigFunction('iterator_to_array', 'iterator_to_array'),
        ];
    }
}
