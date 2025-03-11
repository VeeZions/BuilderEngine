<?php

namespace VeeZions\BuilderEngine\Constant;

use Symfony\Contracts\Translation\TranslatorInterface;
use VeeZions\BuilderEngine\Entity\BuilderArticle;
use VeeZions\BuilderEngine\Entity\BuilderCategory;
use VeeZions\BuilderEngine\Entity\BuilderNavigation;
use VeeZions\BuilderEngine\Entity\BuilderPage;

class TableConstant
{
    public const VBE_TABLE_CREATE = 'create';
    public const VBE_TABLE_FILTERS = 'filters';
    public const VBE_TABLE_COUNTER = 'counter';
    public const VBE_TABLE_TABLE = 'table';
    public const VBE_TABLE_PAGINATION = 'pagination';

    public function getColumnsFromTable(string $entity): array
    {
        $entities = [
            BuilderCategory::class,
            BuilderArticle::class,
            BuilderPage::class,
            BuilderNavigation::class,
        ];

        if (!in_array($entity, $entities, true)) {
            throw new \Exception('Entity "'.$entity.'" not found');
        }

        $columns = match ($entity) {
            BuilderCategory::class => $this->getQueryForCategoryTable(),
            BuilderArticle::class => $this->getQueryForArticleTable(),
            BuilderPage::class => $this->getQueryForPageTable(),
            BuilderNavigation::class => $this->getQueryFornavigationTable(),
            default => null
        };

        if (null === $columns) {
            throw new \Exception('Entity "'.$entity.'" not found');
        }

        return $columns;
    }

    public function getQueryForCategoryTable(): array
    {
        return [
            'c.title' => 'vbe.query.title',
            'c.locale' => 'vbe.query.locale',
            'cp.title as parent' => 'vbe.query.parent',
            'c.createdAt' => 'vbe.query.created.at',
        ];
    }

    public function getQueryForArticleTable(): array
    {
        return [
            'a.title' => 'vbe.query.title',
            'a.published' => 'vbe.query.published',
            'a.locale' => 'vbe.query.locale',
            'a.author' => 'vbe.query.author',
            'a.createdAt' => 'vbe.query.created.at',
        ];
    }

    public function getQueryForPageTable(): array
    {
        return [
            'p.title' => 'vbe.query.title',
            'p.published' => 'vbe.query.published',
            'p.locale' => 'vbe.query.locale',
            'p.route' => 'vbe.query.route',
            'p.createdAt' => 'vbe.query.created.at',
        ];
    }

    public function getQueryForNavigationTable(): array
    {
        return [
            'n.type' => 'vbe.query.type',
            'n.locale' => 'vbe.query.locale',
        ];
    }
}
