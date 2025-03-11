<?php

namespace VeeZions\BuilderEngine\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use VeeZions\BuilderEngine\Entity\BuilderNavigation;
use VeeZions\BuilderEngine\Trait\SearchTrait;

/**
 * @extends ServiceEntityRepository<BuilderNavigation>
 */
class BuilderNavigationRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly PaginatorInterface $paginator,
    ) {
        parent::__construct($registry, BuilderNavigation::class);
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
        array_unshift($columns, 'n.id');
        $query = $this->createQueryBuilder('n')->select($columns);

        return $this->paginator->paginate($query, $page, 10);
    }
}
