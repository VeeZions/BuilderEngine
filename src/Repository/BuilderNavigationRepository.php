<?php

namespace VeeZions\BuilderEngine\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RequestStack;
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
        private readonly RequestStack $requestStack,
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
        $query = $this->createQueryBuilder('n')->select($columns);

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
