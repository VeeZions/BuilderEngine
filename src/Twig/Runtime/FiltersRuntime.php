<?php

namespace VeeZions\BuilderEngine\Twig\Runtime;

use DateTimeInterface;
use Symfony\Component\Form\FormView;
use Twig\Extension\RuntimeExtensionInterface;
use Twig\Markup;
use VeeZions\BuilderEngine\Entity\BuilderArticle;
use VeeZions\BuilderEngine\Entity\BuilderCategory;
use VeeZions\BuilderEngine\Entity\BuilderLibrary;
use VeeZions\BuilderEngine\Entity\BuilderNavigation;
use VeeZions\BuilderEngine\Entity\BuilderPage;
use VeeZions\BuilderEngine\Manager\HtmlManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Carbon\Carbon;

final class FiltersRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private HtmlManager $htmlManager,
        private RequestStack $requestStack,
    )
    {
        
    }

    public function containerId(): ?string
    {
        $route = $this->requestStack->getCurrentRequest()->attributes->get('_route');
        if (!str_starts_with($route, 'xlxeb_controller_')) {
            return null;
        }

        return sprintf(
            'vbe-%s',
            str_replace(
                ['xlxeb_controller_', '_', '.', 'index'],
                ['', '-', '-', 'crud'],
                $route
            )
        );
    }

    public function buildRender(PaginationInterface $data, array $order = []): Markup
    {
        return new Markup($this->htmlManager->buildRender($data, $order), 'UTF-8');
    }

    public function buildTable(PaginationInterface $data): Markup
    {
        return new Markup($this->htmlManager->buildTable($data), 'UTF-8');
    }

    public function buildFilters(PaginationInterface $data): Markup
    {
        return new Markup($this->htmlManager->buildFilters($data), 'UTF-8');
    }

    public function buildPagination(PaginationInterface $data): Markup
    {
        return new Markup($this->htmlManager->buildPagination($data), 'UTF-8');
    }

    public function buildCounter(PaginationInterface $data): Markup
    {
        return new Markup($this->htmlManager->buildCounter($data), 'UTF-8');
    }

    public function buildCreate(PaginationInterface $data): Markup
    {
        return new Markup($this->htmlManager->buildCreate($data), 'UTF-8');
    }

    public function valueMutator(mixed $value): ?string
    {
        if ($value instanceof DateTimeInterface) {
            Carbon::setLocale($this->requestStack->getCurrentRequest()->getLocale());
            return Carbon::parse($value)->isoFormat('lll');
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        return null;
    }

    public function isSortable(string $column): bool
    {
        return !str_contains($column, ' as ');
    }

    public function actionsButtons(FormView $form): Markup
    {
        $data = $form->vars['data'];
        $entity = match (true) {
            $data instanceof BuilderCategory => 'categories',
            $data instanceof BuilderArticle => 'articles',
            $data instanceof BuilderPage => 'pages',
            $data instanceof BuilderNavigation => 'navigations',
            $data instanceof BuilderLibrary => 'libraries',
            default => null,
        };

        if (null === $entity) {
            throw new \Exception('Entity not found');
        }

        return new Markup($this->htmlManager->buildActions($entity), 'UTF-8');
    }
}
