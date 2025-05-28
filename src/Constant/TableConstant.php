<?php

namespace VeeZions\BuilderEngine\Constant;

use Symfony\Contracts\Translation\TranslatorInterface;
use VeeZions\BuilderEngine\Entity\BuilderArticle;
use VeeZions\BuilderEngine\Entity\BuilderCategory;
use VeeZions\BuilderEngine\Entity\BuilderNavigation;
use VeeZions\BuilderEngine\Entity\BuilderPage;
use VeeZions\BuilderEngine\Provider\AuthorProvider;

class TableConstant
{
    public const VBE_TABLE_CREATE = 'create';
    public const VBE_TABLE_FILTERS = 'filters';
    public const VBE_TABLE_COUNTER = 'counter';
    public const VBE_TABLE_TABLE = 'table';
    public const VBE_TABLE_PAGINATION = 'pagination';
    
    public function __construct(
        private AuthorProvider $authorProvider,
        private array $authors
    )
    {
        
    }

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
            'c.id' => 'vbe.query.id',
            'c.title' => 'vbe.query.title',
            'c.locale' => 'vbe.query.locale',
            'cp.title as parent' => 'vbe.query.parent',
            'c.createdAt' => 'vbe.query.created.at',
        ];
    }

    public function getQueryForArticleTable(): array
    {
        $query = [
            'a.id' => 'vbe.query.id',
            'a.title' => 'vbe.query.title',
            'a.locale' => 'vbe.query.locale',
            'a.published' => 'vbe.query.published',
        ];

        $provider = $this->authors['articles'];
        $class = $provider['author_class'];
        if ($class !== null && class_exists($class)) {
            $count = count($this->authorProvider->provideAuthors($provider, 'articles'));
            if ($count > 0) {
                $index = $provider['author_placeholder'] === 'id'
                    ? 'pr.id as prId'
                    : 'pr.'.$provider['author_placeholder'];
                $query[$index] = 'vbe.query.author';
            }
        }

        $query['a.createdAt'] = 'vbe.query.created.at';

        return $query;
    }

    public function getQueryForPageTable(): array
    {
        $query = [
            'p.id' => 'vbe.query.id',
            'p.title' => 'vbe.query.title',
            'p.locale' => 'vbe.query.locale',
            'p.published' => 'vbe.query.published',
        ];

        $provider = $this->authors['pages'];
        $class = $provider['author_class'];
        if ($class !== null && class_exists($class)) {
            $count = count($this->authorProvider->provideAuthors($provider, 'pages'));
            if ($count > 0) {
                $index = $provider['author_placeholder'] === 'id'
                    ? 'pr.id as prId'
                    : 'pr.'.$provider['author_placeholder'];
                $query[$index] = 'vbe.query.author';
            }
        }

        $query['p.createdAt'] = 'vbe.query.created.at';

        return $query;
    }

    public function getQueryForNavigationTable(): array
    {
        return [
            'n.id' => 'vbe.query.id',
            'n.type' => 'vbe.query.type',
            'n.locale' => 'vbe.query.locale',
        ];
    }
}
