<?php

namespace VeeZions\BuilderEngine\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use VeeZions\BuilderEngine\Entity\BuilderPage;

/**
 * @extends ServiceEntityRepository<BuilderPage>
 */
class BuilderPageRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly PaginatorInterface $paginator,
    ) {
        parent::__construct($registry, BuilderPage::class);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getPage(string $route, string $locale): ?array
    {
        $locale = in_array(strtolower($locale), $this->availableTranslations, true) ? $locale : 'En';
        $page = $this->createQueryBuilder('p')
            ->select(
                'p.id',
                "p.title{$locale} as title",
                'p.content as content',
                'p.seo',
                'p.route',
                'p.createdAt',
                'p.updatedAt',
            )
            ->where('p.route = :route')
            ->setParameter('route', $route)
            ->getQuery()
            ->getOneOrNullResult();

        return is_array($page) ? $page : null;
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
        array_unshift($columns, 'p.id');
        $query = $this->createQueryBuilder('p')->select($columns);

        return $this->paginator->paginate($query, $page, 10);
    }
}
