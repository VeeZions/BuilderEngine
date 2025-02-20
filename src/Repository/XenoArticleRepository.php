<?php

namespace XenoLab\XenoEngine\Repository;

use XenoLab\XenoEngine\Entity\XenoArticle;
use XenoLab\XenoEngine\Entity\XenoCategory;
use XenoLab\XenoEngine\Trait\SearchTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<XenoArticle>
 */
class XenoArticleRepository extends ServiceEntityRepository
{
    //use SearchTrait;

    /**
     * @param array<int, string> $availableTranslations
     */
    public function __construct(
        ManagerRegistry $registry,
        //private readonly PaginatorInterface $paginator,
    ) {
        parent::__construct($registry, XenoArticle::class);
    }

    /**
     * @return array<string, mixed>|null
     */
    /*public function getArticle(string $slug, ?XenoCategory $category, string $locale): ?array
    {
        $locale = in_array(strtolower($locale), $this->availableTranslations, true) ? $locale : 'En';
        $article = $this->createQueryBuilder('a')
            ->select(
                'a.id',
                'a.category',
                'a.author',
                'a.updatedAt',
                'a.seo',
                "a.content{$locale} as content",
                'a.hero_image',
                'a.published',
                'a.createdAt',
                'a.updatedAt',
                "a.title{$locale} as title",
                "a.slug{$locale} as slug",
            )
            ->where('a.slug'.$locale.' = :slug')
            ->setParameter('slug', $slug)
            ->andWhere('a.category = :category')
            ->setParameter('category', $category)
            ->getQuery()
            ->getOneOrNullResult();

        return is_array($article) ? $article : null;
    }*/

    /**
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
        $query = $this->createQueryBuilder('a')->select($columns);
        $query = $this->scopeSearch($query, $search, $searchKeys);

        return $this->paginator->paginate($query->orderBy('a.createdAt', $order), $page, 10);
    }*/
}
