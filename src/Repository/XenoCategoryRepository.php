<?php

namespace XenoLab\XenoEngine\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use XenoLab\XenoEngine\Entity\XenoCategory;
use XenoLab\XenoEngine\Trait\SearchTrait;

/**
 * @extends ServiceEntityRepository<XenoCategory>
 */
class XenoCategoryRepository extends ServiceEntityRepository
{
    // use SearchTrait;

    public function __construct(
        ManagerRegistry $registry,
        // private readonly PaginatorInterface $paginator,
    ) {
        parent::__construct($registry, XenoCategory::class);
    }

    /*public function getCategory(string $slug, string $locale): ?array
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
     * @param array<string, string>                  $search
     * @param array<int, string>                     $columns
     * @param array<int, array<string, string|null>> $searchKeys
     *
     * @return PaginationInterface<int, mixed>
     */
    /*public function paginate(
        int $page,
        array $search,
        string $order,
        array $columns,
        array $searchKeys,
    ): PaginationInterface {
        $query = $this->createQueryBuilder('c')->select($columns);
        $query = $this->scopeSearch($query, $search, $searchKeys);

        return $this->paginator->paginate($query->orderBy('c.createdAt', $order), $page, 10);
    }*/
}
