<?php

namespace VeeZions\BuilderEngine\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use VeeZions\BuilderEngine\Constant\TableConstant;
use VeeZions\BuilderEngine\Entity\BuilderArticle;
use VeeZions\BuilderEngine\Entity\BuilderCategory;

/**
 * @extends ServiceEntityRepository<BuilderArticle>
 */
class BuilderArticleRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly PaginatorInterface $paginator,
        private readonly RequestStack $requestStack,
        private readonly array $authors,
        private readonly TableConstant $tableConstant,
    ) {
        parent::__construct($registry, BuilderArticle::class);
    }

    /*
     * @return array<string, mixed>|null
     */
    public function getArticle(string $slug, ?BuilderCategory $category, string $locale): ?array
    {
        $locale = in_array(strtolower($locale), $this->availableTranslations, true) ? $locale : 'En';
        $article = $this->createQueryBuilder('a')
            ->select(
                'a.id',
                'a.category',
                'a.author',
                'a.updatedAt',
                'a.seo',
                "a.content{$locale} as content",
                'a.hero_image',
                'a.published',
                'a.createdAt',
                'a.updatedAt',
                "a.title{$locale} as title",
                "a.slug{$locale} as slug",
            )
            ->where('a.slug'.$locale.' = :slug')
            ->setParameter('slug', $slug)
            ->andWhere('a.category = :category')
            ->setParameter('category', $category)
            ->getQuery()
            ->getOneOrNullResult();

        return is_array($article) ? $article : null;
    }

    /**
     * @param array<int, string>                     $columns
     *
     * @return PaginationInterface<int, mixed>
     */
    public function paginate(
        int $page,
        array $columns,
    ): PaginationInterface {
        $query = $this->createQueryBuilder('a')->select($columns);
        if ($this->authors['author_class'] !== null) {
            $query->leftJoin($this->authors['author_class'], 'pr', 'WITH', 'a.author = pr.id');
        }

        $searchField = $this->requestStack->getCurrentRequest()->query->get('vbeFilterField');
        $searchValue = $this->requestStack->getCurrentRequest()->query->get('vbeFilterValue');
        if ($searchField !== null && $searchValue !== null && strlen($searchValue) > 0) {
            $query->where($searchField.' LIKE :search')
                ->setParameter('search', '%'.$searchValue.'%')
            ;
        }

        return $this->paginator->paginate($query, $page, 10);
    }
}
