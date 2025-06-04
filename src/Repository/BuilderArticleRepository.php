<?php

namespace VeeZions\BuilderEngine\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use VeeZions\BuilderEngine\Entity\BuilderArticle;

/**
 * @extends ServiceEntityRepository<BuilderArticle>
 */
class BuilderArticleRepository extends ServiceEntityRepository
{
    /**
     * @param array<string, mixed> $authors
     */
    public function __construct(
        ManagerRegistry $registry,
        private readonly PaginatorInterface $paginator,
        private readonly RequestStack $requestStack,
        private readonly array $authors,
    ) {
        parent::__construct($registry, BuilderArticle::class);
    }

    /**
     * @param array<int, string> $columns
     *
     * @return PaginationInterface<int, mixed>
     */
    public function paginate(
        int $page,
        array $columns,
        int $limit,
    ): PaginationInterface {
        $query = $this->createQueryBuilder('a')->select($columns);
        if (is_string($this->authors['author_class'])) {
            $query->leftJoin($this->authors['author_class'], 'pr', 'WITH', 'a.author = pr.id');
        }

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

        return $this->paginator->paginate($query, $page, $limit);
    }

    /**
     * @return PaginationInterface<int, mixed>|null
     */
    public function getBlog(string $locale, Request $request, int $limit): ?PaginationInterface
    {
        $page = $request->query->getInt('page', 1);
        $query = $this->createQueryBuilder('a');
        if (is_string($this->authors['author_class'])) {
            $query->leftJoin($this->authors['author_class'], 'pr', 'WITH', 'a.author = pr.id');
        }

        return $this->paginator->paginate($query, $page, $limit);
    }
}
