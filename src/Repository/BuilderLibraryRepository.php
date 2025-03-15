<?php

namespace VeeZions\BuilderEngine\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use VeeZions\BuilderEngine\Constant\AssetConstant;
use VeeZions\BuilderEngine\Entity\BuilderLibrary;

/**
 * @extends ServiceEntityRepository<BuilderLibrary>
 */
class BuilderLibraryRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly PaginatorInterface $paginator,
        private readonly RequestStack $requestStack,
    ) {
        parent::__construct($registry, BuilderLibrary::class);
    }

    /**
     * @param array<int, string>                     $columns
     *
     * @return PaginationInterface<int, mixed>
     */
    public function paginate(): PaginationInterface {
        $query = $this->createQueryBuilder('l');
        $page = $this->requestStack->getCurrentRequest()->query->getInt('page', 1);

        $searchField = $this->requestStack->getCurrentRequest()->query->get('vbeFilterField');
        $searchValue = $this->requestStack->getCurrentRequest()->query->get('vbeFilterValue');
        if ($searchField !== null && $searchValue !== null) {
            $query->where($searchField.' LIKE :search')
                ->setParameter('search', '%'.$searchValue.'%')
            ;
        }

        return $this->paginator->paginate($query, $page, 10);
    }

    /**
     * @param array<int, int> $ids
     *
     * @return array<int, BuilderLibrary>
     */
    public function getSelectedGeds(array $ids): array
    {
        $geds = $this->createQueryBuilder('g')
            ->andWhere('g.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult();

        return is_array($geds) ? $geds : [];
    }
}
