<?php

namespace VeeZions\BuilderEngine\Twig\Runtime;

use Twig\Extension\RuntimeExtensionInterface;
use VeeZions\BuilderEngine\Manager\HtmlManager;

final class FiltersRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private HtmlManager $htmlManager,
    )
    {
        
    }
    
    public function buildTable(array $data): string
    {
        return $this->htmlManager->buildTable($data);
    }
}
