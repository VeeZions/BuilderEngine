<?php

namespace VeeZions\BuilderEngine\Manager;

use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use VeeZions\BuilderEngine\Constant\ConfigConstant;
use VeeZions\BuilderEngine\Constant\TableConstant;
use VeeZions\BuilderEngine\Entity\BuilderArticle;
use VeeZions\BuilderEngine\Entity\BuilderCategory;
use VeeZions\BuilderEngine\Entity\BuilderNavigation;
use VeeZions\BuilderEngine\Entity\BuilderPage;
use VeeZions\BuilderEngine\Trait\AccessTrait;

readonly class HtmlManager
{
    use AccessTrait;

    /**
     * @param array<string, array<string, string>>               $customRoutes
     * @param array<string, array<string, array<string, mixed>>> $actions
     */
    public function __construct(
        private Environment $twig,
        private TranslatorInterface $translator,
        private RequestStack $requestStack,
        private TableConstant $constant,
        private AuthorizationCheckerInterface $authorizationChecker,
        private Router $router,
        private array $customRoutes,
        private array $actions,
    ) {
    }

    /**
     * @param array<int, string>              $order
     * @param PaginationInterface<int, mixed> $data
     */
    public function buildRender(
        PaginationInterface $data,
        array $order = [],
    ): string {
        $defaultOrder = [
            TableConstant::VBE_TABLE_CREATE,
            TableConstant::VBE_TABLE_FILTERS,
            TableConstant::VBE_TABLE_COUNTER,
            TableConstant::VBE_TABLE_TABLE,
            TableConstant::VBE_TABLE_PAGINATION,
        ];

        if (empty($order)) {
            $order = $defaultOrder;
        } else {
            foreach ($order as $stage) {
                if (!in_array($stage, $defaultOrder, true)) {
                    $default = implode(', ', $defaultOrder);
                    throw new \Exception(sprintf('%s is not a valid order item. Order items can be: "%s".', $stage, $default));
                }
            }
        }

        return $this->twig->render(ConfigConstant::CONFIG_INTERNAL_TEMPLATE_PATH.'/table/render.html.twig', [
            'data' => $data,
            'order' => $order,
        ]);
    }

    /**
     * @param PaginationInterface<int, mixed> $data
     */
    public function buildTable(PaginationInterface $data): string
    {
        $rows = array_map(function ($row) {
            if (!is_array($row)) {
                throw new \Exception('Row must be an array');
            }
            $r = [
                'cells' => (function () use ($row) {
                    $cells = [];
                    foreach ($row as $key => $value) {
                        $cells[] = [
                            'label' => $key,
                            'value' => $value,
                        ];
                    }

                    return $cells;
                })(),
                'actions' => $this->setActions($row['id']),
            ];

            return $r;
        }, (array) $data->getItems());

        $dataFromRoute = $this->getDataFromRoute()['entity'];
        if (!is_string($dataFromRoute)) {
            throw new \Exception('Entity must be a string');
        }

        return $this->twig->render(ConfigConstant::CONFIG_INTERNAL_TEMPLATE_PATH.'/table/table.html.twig', [
            'head' => $this->prepareTableHead($this->getConstantsByEntity($dataFromRoute)),
            'rows' => $rows,
            'data' => $data,
        ]);
    }

    /**
     * @param PaginationInterface<int, mixed> $data
     */
    public function buildFilters(PaginationInterface $data): string
    {
        $dataFromRoute = $this->getDataFromRoute()['entity'];
        if (!is_string($dataFromRoute)) {
            throw new \Exception('Entity must be a string');
        }

        return $this->twig->render(ConfigConstant::CONFIG_INTERNAL_TEMPLATE_PATH.'/table/filters.html.twig', [
            'data' => $data,
            'filters' => $this->getConstantsByEntity($dataFromRoute),
        ]);
    }

    /**
     * @param PaginationInterface<int, mixed> $data
     */
    public function buildCounter(PaginationInterface $data): string
    {
        $firstItem = $data->getItemNumberPerPage() * ($data->getCurrentPageNumber() - 1) + 1;

        return $this->twig->render(ConfigConstant::CONFIG_INTERNAL_TEMPLATE_PATH.'/table/counter.html.twig', [
            'pages' => (int) ceil($data->getTotalItemCount() / $data->getItemNumberPerPage()),
            'page' => $data->getCurrentPageNumber(),
            'bookmark_start' => $firstItem,
            'bookmark_end' => $firstItem + count((array) $data->getItems()) - 1,
            'totalItems' => $data->getTotalItemCount(),
        ]);
    }

    /**
     * @param PaginationInterface<int, mixed> $data
     */
    public function buildPagination(PaginationInterface $data): string
    {
        return $this->twig->render(ConfigConstant::CONFIG_INTERNAL_TEMPLATE_PATH.'/table/pagination.html.twig', [
            'pagination' => $data,
        ]);
    }

    /**
     * @param PaginationInterface<int, mixed> $data
     */
    public function buildCreate(PaginationInterface $data): string
    {
        $rules = $this->getAllowedActions()['new'];
        if (!is_array($rules['roles'])) {
            throw new \Exception('Roles must be an array');
        }

        $dataFromRoute = $this->getDataFromRoute()['routes'];
        if (!is_array($dataFromRoute)) {
            throw new \Exception('Routes must be a string');
        }

        return $this->twig->render(ConfigConstant::CONFIG_INTERNAL_TEMPLATE_PATH.'/table/create.html.twig', [
            'isAllowed' => true === $rules['enabled'] && $this->isGranted($rules['roles'], false),
            'new_entity' => [
                'route' => $dataFromRoute['new'],
                'label' => $this->translator->trans('vbe.new.entity', [], 'BuilderEngineBundle-table'),
            ],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function getDataFromRoute(): array
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            throw new \Exception('No request found');
        }
        $route = $request->get('_route');
        if (!is_string($route)) {
            throw new \Exception('Route must be a string');
        }
        $entityClass = null;
        $concernedRoutes = null;

        foreach ($this->customRoutes as $entity => $routes) {
            foreach ($routes as $r) {
                if ($routes['list'] === $route) {
                    $entityClass = match ($entity) {
                        'articles_routes' => BuilderArticle::class,
                        'categories_routes' => BuilderCategory::class,
                        'pages_routes' => BuilderPage::class,
                        'navigations_routes' => BuilderNavigation::class,
                        default => null,
                    };

                    $concernedRoutes = $routes;
                }
            }
        }

        if (null === $entityClass || null === $concernedRoutes) {
            throw new RouteNotFoundException(sprintf('"%s" is not a valid route.', $route));
        }

        return [
            'entity' => $entityClass,
            'routes' => $concernedRoutes,
        ];
    }

    /**
     * @return array<string, string>
     */
    private function getConstantsByEntity(string $type): array
    {
        $match = match ($type) {
            BuilderCategory::class => $this->constant->getQueryForCategoryTable(),
            BuilderArticle::class => $this->constant->getQueryForArticleTable(),
            BuilderPage::class => $this->constant->getQueryForPageTable(),
            BuilderNavigation::class => $this->constant->getQueryFornavigationTable(),
            default => null,
        };

        if (null === $match) {
            throw new \Exception('Entity "'.$type.'" not found');
        }

        return $match;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function getAllowedActions(): array
    {
        $info = $this->getDataFromRoute();
        $actionsIndex = match ($info['entity']) {
            BuilderCategory::class => 'categories',
            BuilderArticle::class => 'articles',
            BuilderPage::class => 'pages',
            BuilderNavigation::class => 'navigations',
            default => null,
        };

        if (null === $actionsIndex) {
            throw new \Exception('Entity "'.$info['entity'].'" not found');
        }

        return $this->actions[$actionsIndex];
    }

    /**
     * @param array<string, string> $constants
     *
     * @return array<int, array<string, mixed>>
     */
    private function prepareTableHead(array $constants): array
    {
        $head = [];
        foreach ($constants as $key => $value) {
            $head[] = [
                'label' => $this->translator->trans($value, [], 'BuilderEngineBundle-table'),
                'column' => $key,
            ];
        }

        return $head;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function setActions(int $id): array
    {
        $actionRules = $this->getAllowedActions();
        $actions = [];
        $routes = $this->router->getRouteCollection();

        foreach ($actionRules as $type => $actionRule) {
            if (in_array($type, ['show', 'edit', 'delete'], true)
                && true === $actionRule['enabled']
                && is_array($actionRule['roles'])
                && $this->isGranted($actionRule['roles'], false)) {
                $info = $this->getDataFromRoute();
                $entity = match ($info['entity']) {
                    BuilderCategory::class => 'category',
                    BuilderArticle::class => 'article',
                    BuilderPage::class => 'page',
                    BuilderNavigation::class => 'navigation',
                    default => null,
                };

                if (null === $entity) {
                    throw new \Exception('Entity "'.$info['entity'].'" not found');
                }

                $onclick = 'delete' === $type ? 'return confirm("'.$this->translator->trans(
                    'vbe.action.confirm.delete',
                    [
                        '%entity%' => $this->translator->trans('vbe.entity.'.$entity, [], 'BuilderEngineBundle-table'),
                        '%nb%' => $id,
                    ],
                    'BuilderEngineBundle-table'
                ).'")' : null;

                if (!is_array($info['routes'])) {
                    throw new \Exception('Routes must be an array');
                }

                $route = $routes->get($info['routes'][$type]);
                if (null === $route) {
                    throw new RouteNotFoundException(sprintf('"%s" is not a valid route.', $info['routes'][$type]));
                }
                if (!str_contains($route->getPath(), '{id}')) {
                    throw new InvalidParameterException(sprintf('"%s" route must contain "{id}" parameter.', $route->getPath()));
                }

                $actions[$type] = [
                    'url' => $this->router->generate($info['routes'][$type], ['id' => $id]),
                    'class' => 'vbe-action vbe-action-'.$type,
                    'onclick' => $onclick,
                    'label' => 'vbe.action.label.'.$type,
                ];
            }
        }

        return $actions;
    }
}
