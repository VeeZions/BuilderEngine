<?php

namespace VeeZions\BuilderEngine\Loader;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\RouteCollection;

class BuilderEngineLoader extends Loader
{
    public function __construct(
        private string $mode,
        private string $prefix,
        private array $actionsConfig = [],
    ) {
    }

    public function load($resource, ?string $type = null): RouteCollection
    {
        $routes = new RouteCollection();
        $type = 'yaml';

        $asyncRoute = $this->import('@BuilderEngineBundle/config/routing/async.yaml', $type);
        $asyncRoute->addPrefix('/_xlxeb');
        $routes->addCollection($asyncRoute);

        if ('form' !== $this->mode) {
            foreach ($this->actionsConfig as $entity => $actions) {
                $index = sprintf(
                    '@BuilderEngineBundle/config/routing/controllers/%s/index.yaml',
                    $entity
                );
                
                $indexRoute = $this->import($index, $type);
                if (null !== $this->prefix) {
                    $indexRoute->addPrefix($this->prefix);
                }
                
                $routes->addCollection($indexRoute);
                foreach ($actions as $action => $config) {
                    if (true === $config['enabled'] && $entity !== 'libraries') {
                        
                        $routing = sprintf(
                            '@BuilderEngineBundle/config/routing/controllers/%s/%s.yaml',
                            $entity,
                            $action
                        );

                        /**@var \Symfony\Component\Routing\RouteCollection $controllersRoute*/
                        $controllersRoute = $this->import($routing, $type);
                        if (null !== $this->prefix) {
                            $controllersRoute->addPrefix($this->prefix);
                        }

                        $routes->addCollection($controllersRoute);
                    }
                }
            }
        }

        return $routes;
    }

    public function supports($resource, ?string $type = null): bool
    {
        return 'builder' === $type;
    }
}
