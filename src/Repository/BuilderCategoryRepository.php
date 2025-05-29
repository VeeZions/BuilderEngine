<?php

namespace VeeZions\BuilderEngine\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use VeeZions\BuilderEngine\Entity\BuilderCategory;

/**
 * @extends ServiceEntityRepository<BuilderCategory>
 */
class BuilderCategoryRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly PaginatorInterface $paginator,
        private readonly RequestStack $requestStack,
    ) {
        parent::__construct($registry, BuilderCategory::class);
    }

    /**
     * @param array<int, string> $columns
     *
     * @return PaginationInterface<int, mixed>
     */
    public function paginate(
        int $page,
        array $columns,
    ): PaginationInterface {
        $query = $this->createQueryBuilder('c')
            ->select($columns)
            ->leftJoin(BuilderCategory::class, 'cp', 'WITH', 'c.parent = cp.id');

        $request = $this->requestStack->getCurrentRequest();
        if (null !== $request) {
            $searchField = $request->query->get('vbeFilterField');
            $searchValue = $request->query->get('vbeFilterValue');
            if (is_string($searchField) && is_string($searchValue) && strlen($searchValue) > 0) {
                $query->where($searchField.' LIKE :search')
                    ->setParameter('search', '%'.$searchValue.'%')
                ;
            }
        }

        return $this->paginator->paginate($query, $page, 10);
    }
}
