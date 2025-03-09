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
            new TwigFilter('vbe_build_table', [FiltersRuntime::class, 'buildTable']),
        ];
    }

    public function getFunctions(): array
    {
        return [
        ];
    }
}
