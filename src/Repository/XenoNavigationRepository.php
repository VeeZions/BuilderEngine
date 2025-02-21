<?php

namespace XenoLab\XenoEngine\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use XenoLab\XenoEngine\Entity\XenoNavigation;
use XenoLab\XenoEngine\Trait\SearchTrait;

/**
 * @extends ServiceEntityRepository<XenoNavigation>
 */
class XenoNavigationRepository extends ServiceEntityRepository
{
    // use SearchTrait;

    public function __construct(
        ManagerRegistry $registry,
        // private readonly PaginatorInterface $paginator,
    ) {
        parent::__construct($registry, XenoNavigation::class);
    }

    /*
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
        $query = $this->createQueryBuilder('n')->select($columns);
        $query = $this->scopeSearch($query, $search, $searchKeys);

        return $this->paginator->paginate($query->orderBy('n.id', $order), $page, 10);
    }*/
}
