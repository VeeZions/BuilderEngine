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
        private readonly RequestStack $requestStack, /**@phpstan-ignore-line */
    ) {
        parent::__construct($registry, BuilderLibrary::class);
    }

    /**
     * @param array<int, string> $types
     *
     * @return PaginationInterface<int, mixed>
     */
    public function paginate(
        ?string $order = 'asc',
        ?array $types = [],
        ?string $search = null,
        ?int $count = null,
    ): PaginationInterface {
        $query = $this->createQueryBuilder('l');
        if (null !== $search) {
            $query->where('l.title LIKE :search')
                ->setParameter('search', '%'.$search.'%')
            ;
        }

        if (null !== $types && count($types) > 0) {
            $query->andWhere('l.type IN (:types)')
                ->setParameter('types', $types)
            ;
        }

        return $this->paginator->paginate($query->orderBy('l.id', $order), 1, $count ?? AssetConstant::ITEMS_PER_LOAD);
    }

    /**
     * @param array<int, int> $ids
     *
     * @return array<int, BuilderLibrary>
     */
    public function getSelectedMedia(array $ids): array
    {
        $media = $this->createQueryBuilder('g')
            ->andWhere('g.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult();

        return is_array($media) ? $media : [];
    }
}
