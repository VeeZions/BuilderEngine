<?php

namespace VeeZions\BuilderEngine\Manager;

use Safe\Exceptions\ArrayException;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use VeeZions\BuilderEngine\Constant\Crud\ArticleConstant;
use VeeZions\BuilderEngine\Constant\Crud\CategoryConstant;
use VeeZions\BuilderEngine\Constant\Crud\NavigationConstant;
use VeeZions\BuilderEngine\Constant\Crud\PageConstant;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Twig\Environment;
use VeeZions\BuilderEngine\Constant\TableConstant;
use Symfony\Component\HttpFoundation\RequestStack;
use VeeZions\BuilderEngine\Entity\BuilderArticle;
use VeeZions\BuilderEngine\Entity\BuilderCategory;
use VeeZions\BuilderEngine\Entity\BuilderNavigation;
use VeeZions\BuilderEngine\Entity\BuilderPage;
use VeeZions\BuilderEngine\Trait\AccessTrait;
use VeeZions\BuilderEngine\Constant\ConfigConstant;

readonly class HtmlManager
{
    use AccessTrait;

    public function __construct(
        private Environment $twig,
        private TranslatorInterface $translator,
        private RequestStack $requestStack,
        private TableConstant $constant,
        private AuthorizationCheckerInterface $authorizationChecker,
        private Router $router,
        private array $customRoutes,
        private array $actions
    )
    {
        
    }

    public function buildRender(
        PaginationInterface $data, 
        array $order = []
    ): string
    {
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
                    $default = implode(", ", $defaultOrder);
                    throw new ArrayException(sprintf(
                        '%s is not a valid order item. Order items can be: "%s".', 
                        $stage, 
                        $default
                    ));
                }
            }
        }
        
        return $this->twig->render(ConfigConstant::CONFIG_INTERNAL_TEMPLATE_PATH.'/table/render.html.twig', [
            'data' => $data,
            'order' => $order,
        ]);
    }
    
    public function buildTable(PaginationInterface $data): string
    {
        $rows = array_map(function($row) {
            $r = [
                'cells' => (function() use ($row) {
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
        }, $data->getItems());

        return $this->twig->render(ConfigConstant::CONFIG_INTERNAL_TEMPLATE_PATH.'/table/table.html.twig', [
            'head' => $this->prepareTableHead($this->getConstantsByEntity($this->getDataFromRoute()['entity'])),
            'rows' => $rows,
            'data' => $data,
        ]);
    }
    
    public function buildFilters(PaginationInterface $data): string
    {
        return $this->twig->render(ConfigConstant::CONFIG_INTERNAL_TEMPLATE_PATH.'/table/filters.html.twig', [
            'data' => $data,
            'filters' => $this->getConstantsByEntity($this->getDataFromRoute()['entity']),
        ]);
    }
    
    public function buildCounter(PaginationInterface $data): string
    {
        $firstItem = $data->getItemNumberPerPage() * ($data->getCurrentPageNumber() - 1) + 1;
        return $this->twig->render(ConfigConstant::CONFIG_INTERNAL_TEMPLATE_PATH.'/table/counter.html.twig', [
            'pages' => (int) ceil($data->getTotalItemCount() / $data->getItemNumberPerPage()),
            'page' => $data->getCurrentPageNumber(),
            'bookmark_start' => $firstItem,
            'bookmark_end' => $firstItem + count($data->getItems()) - 1,
            'totalItems' => $data->getTotalItemCount(),
        ]);
    }
    
    public function buildPagination(PaginationInterface $data): string
    {
        return $this->twig->render(ConfigConstant::CONFIG_INTERNAL_TEMPLATE_PATH.'/table/pagination.html.twig', [
            'pagination' => $data,
        ]);
    }

    public function buildCreate(PaginationInterface $data): string
    {
        $rules = $this->getAllowedActions()['new'];
        return $this->twig->render(ConfigConstant::CONFIG_INTERNAL_TEMPLATE_PATH.'/table/create.html.twig', [
            'isAllowed' => $rules['enabled'] === true && $this->isGranted($rules['roles'], false),
            'new_entity' => [
                'route' => $this->getDataFromRoute()['routes']['new'],
                'label' => $this->translator->trans('vbe.new.entity', [], 'BuilderEngineBundle-table'),
            ]
        ]);
    }

    private function getDataFromRoute(): array
    {
        $route = $this->requestStack->getCurrentRequest()->get('_route');
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

    private function getConstantsByEntity(string $type): array
    {
        $match = match ($type) {
            BuilderCategory::class => $this->constant->getQueryForCategoryTable(),
            BuilderArticle::class => $this->constant->getQueryForArticleTable(),
            BuilderPage::class => $this->constant->getQueryForPageTable(),
            BuilderNavigation::class => $this->constant->getQueryFornavigationTable(),
            default => null
        };

        if (null === $match) {
            throw new \Exception('Entity "'.$type.'" not found');
        }

        return $match;
    }

    private function getAllowedActions(): array
    {
        $info = $this->getDataFromRoute();
        $actionsIndex = match ($info['entity']) {
            BuilderCategory::class => 'categories',
            BuilderArticle::class => 'articles',
            BuilderPage::class => 'pages',
            BuilderNavigation::class => 'navigations',
            default => null
        };

        if (null === $actionsIndex) {
            throw new \Exception('Entity "'.$info['entity'].'" not found');
        }

        return $this->actions[$actionsIndex];
    }
    
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

    private function setActions(int $id): array
    {
        $actionRules = $this->getAllowedActions();
        $actions = [];
        $routes = $this->router->getRouteCollection();

        foreach ($actionRules as $type => $actionRule) {

            if (in_array($type, ['show', 'edit', 'delete'], true)
                && $actionRule['enabled'] === true
                && $this->isGranted($actionRule['roles'], false))
            {
                $info = $this->getDataFromRoute();
                $entity = match ($info['entity']) {
                    BuilderCategory::class => 'category',
                    BuilderArticle::class => 'article',
                    BuilderPage::class => 'page',
                    BuilderNavigation::class => 'navigation',
                    default => null
                };

                if (null === $entity) {
                    throw new \Exception('Entity "'.$info['entity'].'" not found');
                }

                $onclick = $type === 'delete' ? 'return confirm("'.$this->translator->trans(
                    'vbe.action.confirm.delete',
                    [
                        '%entity%' => $this->translator->trans('vbe.entity.'.$entity, [], 'BuilderEngineBundle-table'),
                        '%nb%' => $id,
                    ],
                    'BuilderEngineBundle-table'
                    ).'")' : null;

                $route = $routes->get($info['routes'][$type]);
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
