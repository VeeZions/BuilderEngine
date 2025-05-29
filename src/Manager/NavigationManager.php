<?php

namespace VeeZions\BuilderEngine\Manager;

use Doctrine\ORM\EntityManagerInterface;
use VeeZions\BuilderEngine\Entity\BuilderArticle;
use VeeZions\BuilderEngine\Entity\BuilderCategory;
use VeeZions\BuilderEngine\Entity\BuilderPage;

readonly class NavigationManager
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function getDataForNavigationBuilder(?string $locale): array
    {
        if (null === $locale) {
            return [];
        }

        return [
            'pages' => array_map(function ($page) {
                return [
                    'type' => 'page',
                    'label' => $page->getTitle(),
                    'id' => $page->getId(),
                    'query' => [],
                    'route' => $page->getRoute(),
                    'link' => null,
                    'blank' => false,
                ];
            }, $this->entityManager->getRepository(BuilderPage::class)->findBy(['locale' => $locale])),

            'articles' => array_map(function ($article) {
                return [
                    'type' => 'article',
                    'label' => $article->getTitle(),
                    'id' => $article->getId(),
                    'query' => [
                        'categories' => static function () use ($article) {
                            $categories = [];
                            foreach ($article->getCategories() as $category) {
                                $categories[] = $category->getSlug();
                            }

                            return $categories;
                        },
                        'article' => $article->getSlug(),
                    ],
                    'route' => 'app_web_blog_article',
                    'link' => null,
                    'blank' => false,
                ];
            }, $this->entityManager->getRepository(BuilderArticle::class)->findBy(['published' => true, 'locale' => $locale])),

            'categories' => array_map(function ($category) {
                return [
                    'type' => 'category',
                    'label' => $category->getTitle(),
                    'id' => $category->getId(),
                    'query' => ['category' => $category->getSlug()],
                    'route' => 'app_web_blog_category',
                    'link' => null,
                    'blank' => false,
                ];
            }, $this->entityManager->getRepository(BuilderCategory::class)->findBy(['locale' => $locale])),

            'user' => [],
            'commerce' => [],
            'external' => [],
        ];
    }
}
