<?php

namespace Xenolabs\XenoEngineBundle\Repository;

use Xenolabs\XenoEngineBundle\Constant\AssetManagerConstant;
use Xenolabs\XenoEngineBundle\Entity\Ged;
use Xenolabs\XenoEngineBundle\Trait\SearchTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Ged>
 */
class GedRepository extends ServiceEntityRepository
{
    use SearchTrait;

    public function __construct(
        ManagerRegistry $registry,
        private readonly PaginatorInterface $paginator,
    ) {
        parent::__construct($registry, Ged::class);
    }

    /**
     * @param array<int, string>|null $types
     *
     * @return PaginationInterface<int, mixed>
     */
    public function paginate(
        ?int $count = null,
        ?string $search = null,
        ?array $types = [],
        string $order = 'DESC',
    ): PaginationInterface {
        $query = $this->createQueryBuilder('g');
        if (null !== $search && strlen($search) > 0) {
            $query->andWhere('g.url LIKE :search OR g.mime LIKE :search OR g.legend LIKE :search')
                ->setParameter('search', '%'.$search.'%');
        }
        if (!empty($types)) {
            $query->andWhere('g.type IN (:types)')
                ->setParameter('types', $types);
        }
        $count = null === $count ? AssetManagerConstant::ITEMS_PER_LOAD : $count;

        return $this->paginator->paginate($query->orderBy('g.id', $order), 1, $count);
    }

    /**
     * @param array<int, int> $ids
     *
     * @return array<int, Ged>
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
