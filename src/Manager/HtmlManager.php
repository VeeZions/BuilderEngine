<?php

namespace VeeZions\BuilderEngine\Manager;

use VeeZions\BuilderEngine\Constant\Crud\ArticleConstant;
use VeeZions\BuilderEngine\Constant\Crud\CategoryConstant;
use VeeZions\BuilderEngine\Constant\Crud\NavigationConstant;
use VeeZions\BuilderEngine\Constant\Crud\PageConstant;

readonly class HtmlManager
{
    public function __construct()
    {
        
    }
    
    public function buildTable(array $data): string
    {
        dump($data);
        return 'toto';
    }
}
