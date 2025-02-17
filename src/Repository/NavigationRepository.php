<?php

namespace Xenolabs\XenoEngine\Repository;

use Xenolabs\XenoEngine\Entity\Navigation;
use Xenolabs\XenoEngine\Trait\SearchTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Navigation>
 */
class NavigationRepository extends ServiceEntityRepository
{
    use SearchTrait;

    public function __construct(
        ManagerRegistry $registry,
        private readonly PaginatorInterface $paginator,
    ) {
        parent::__construct($registry, Navigation::class);
    }

    /**
     * @param array<string, string>                  $search
     * @param array<int, string>                     $columns
     * @param array<int, array<string, string|null>> $searchKeys
     *
     * @return PaginationInterface<int, mixed>
     */
    public function paginate(
        int $page,
        array $search,
        string $order,
        array $columns,
        array $searchKeys,
    ): PaginationInterface {
        $query = $this->createQueryBuilder('n')->select($columns);
        $query = $this->scopeSearch($query, $search, $searchKeys);

        return $this->paginator->paginate($query->orderBy('n.id', $order), $page, 10);
    }
}
