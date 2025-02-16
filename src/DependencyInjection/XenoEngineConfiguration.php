<?php

namespace XenoLabs\XenoEngine\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class XenoEngineConfiguration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('xeno_engine');

        // ... add node definitions to the root of the tree
        // $treeBuilder->getRootNode()->...

        return $treeBuilder;
    }
}
