<?php

namespace VeeZions\BuilderEngine\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use VeeZions\BuilderEngine\Entity\BuilderCategory;

/**
 * @extends ServiceEntityRepository<BuilderCategory>
 */
class BuilderCategoryRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly PaginatorInterface $paginator,
    ) {
        parent::__construct($registry, BuilderCategory::class);
    }

    public function getCategory(string $slug, string $locale): ?array
    {
        $locale = in_array(strtolower($locale), $this->availableTranslations, true) ? $locale : 'En';
        $category = $this->createQueryBuilder('c')
            ->select(
                'c.id',
                'c.parent',
                'c.createdAt',
                "c.slug{$locale} as slug",
                "c.title{$locale} as title",
            )
            ->where('c.slug'.$locale.' = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();

        return is_array($category) ? $category : null;
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
        array_unshift($columns, 'c.id');
        $query = $this->createQueryBuilder('c')
            ->select($columns)
            ->leftJoin(BuilderCategory::class, 'cp', 'WITH', 'c.parent = cp.id');

        return $this->paginator->paginate($query, $page, 10);
    }
}
