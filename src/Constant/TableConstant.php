<?php

namespace VeeZions\BuilderEngine\Constant;

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

    /**
     * @param array<string, array<string, mixed>> $authors
     */
    public function __construct(
        private AuthorProvider $authorProvider,
        private array $authors,
    ) {
    }

    /**
     * @return string[]
     */
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
        };

        return $columns;
    }

    /**
     * @return string[]
     */
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

    /**
     * @return string[]
     */
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
        if (is_string($class) && class_exists($class)) {
            $authors = $this->authorProvider->provideAuthors($provider, 'articles');
            if (is_array($authors) && count($authors) > 0) {
                $index = 'id' === $provider['author_placeholder']
                    ? 'pr.id as prId'
                    : 'pr.'.$provider['author_placeholder'];
                $query[$index] = 'vbe.query.author';
            }
        }

        $query['a.createdAt'] = 'vbe.query.created.at';

        return $query;
    }

    /**
     * @return string[]
     */
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
        if (is_string($class) && class_exists($class)) {
            $authors = $this->authorProvider->provideAuthors($provider, 'pages');
            if (is_array($authors) && count($authors) > 0) {
                $index = 'id' === $provider['author_placeholder']
                    ? 'pr.id as prId'
                    : 'pr.'.$provider['author_placeholder'];
                $query[$index] = 'vbe.query.author';
            }
        }

        $query['p.createdAt'] = 'vbe.query.created.at';

        return $query;
    }

    /**
     * @return string[]
     */
    public function getQueryForNavigationTable(): array
    {
        return [
            'n.id' => 'vbe.query.id',
            'n.type' => 'vbe.query.type',
            'n.locale' => 'vbe.query.locale',
        ];
    }
}
