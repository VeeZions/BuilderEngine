<?php

namespace VeeZions\BuilderEngine\Loader;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\RouteCollection;
use VeeZions\BuilderEngine\Constant\ConfigConstant;

class BuilderEngineLoader extends Loader
{
    /**
     * @param array<string, array<string, array<string, mixed>>> $actionsConfig
     * @param array<string, string> $frontRoutes
     */
    public function __construct(
        private string $mode,
        private string $prefix,
        private array $actionsConfig = [],
        protected array $frontRoutes,
    ) {
    }

    public function load(mixed $resource, ?string $type = null): RouteCollection
    {
        $routes = new RouteCollection();
        $type = 'yaml';

        $asyncRoute = $this->import(ConfigConstant::CONFIG_SHARED_TEMPLATE_PATH.'/config/routing/async.yaml', $type);
        $asyncRoute->addPrefix('/_vzbeb'); /* @phpstan-ignore-line */
        $routes->addCollection($asyncRoute); /* @phpstan-ignore-line */

        $defaultFrontRoutes = [
            ConfigConstant::CONFIG_DEFAULT_BLOG_ROUTE,
            ConfigConstant::CONFIG_DEFAULT_ARTICLE_ROUTE,
            ConfigConstant::CONFIG_DEFAULT_CATEGORY_ROUTE
        ];

        foreach ($this->frontRoutes as $target => $frontRoute) {
            if (in_array($frontRoute, $defaultFrontRoutes, true)) {
                $fr = $this->import(
                    ConfigConstant::CONFIG_SHARED_TEMPLATE_PATH.'/config/routing/controllers/front/'.$target.'.yaml',
                    $type
                );
                $routes->addCollection($fr); /* @phpstan-ignore-line */
            }
        }

        if ('form' !== $this->mode) {
            foreach ($this->actionsConfig as $entity => $actions) {
                $index = sprintf(
                    '%s/config/routing/controllers/%s/index.yaml',
                    ConfigConstant::CONFIG_SHARED_TEMPLATE_PATH,
                    $entity
                );

                $indexRoute = $this->import($index, $type);
                if (null !== $this->prefix) {
                    $indexRoute->addPrefix($this->prefix); /* @phpstan-ignore-line */
                }

                $routes->addCollection($indexRoute); /* @phpstan-ignore-line */
                foreach ($actions as $action => $config) {
                    if (true === $config['enabled'] && 'libraries' !== $entity) {
                        $routing = sprintf(
                            '%s/config/routing/controllers/%s/%s.yaml',
                            ConfigConstant::CONFIG_SHARED_TEMPLATE_PATH,
                            $entity,
                            $action
                        );

                        /** @var RouteCollection $controllersRoute */
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

    public function supports(mixed $resource, ?string $type = null): bool
    {
        return 'builder' === $type;
    }
}
