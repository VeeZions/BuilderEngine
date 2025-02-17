<?php

namespace XenoLab\XenoEngine\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class XenoEngineConfiguration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('xeno_engine');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('twitter')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->integerNode('client_id')
                            ->defaultValue(1)
                        ->end()
                        ->scalarNode('client_secret')
                            ->defaultValue('xxx')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
