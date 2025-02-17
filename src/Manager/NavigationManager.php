<?php

namespace XenoLabs\XenoEngine\Manager;

use XenoLabs\XenoEngine\Repository\ArticleRepository;
use XenoLabs\XenoEngine\Repository\CategoryRepository;
use XenoLabs\XenoEngine\Repository\PageRepository;

readonly class NavigationManager
{
    public function __construct(
        private ArticleRepository $articleRepository,
        private PageRepository $pageRepository,
        private CategoryRepository $categoryRepository,
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
            'pages' => array_map(function ($page) use ($locale) {
                return [
                    'type' => 'page',
                    'label' => 'fr' === $locale ? $page->getTitleFr() : $page->getTitleEn(),
                    'id' => $page->getId(),
                    'query' => [],
                    'route' => $page->getRoute(),
                    'link' => null,
                    'blank' => false,
                ];
            }, $this->pageRepository->findAll()),

            'articles' => array_map(function ($article) use ($locale) {
                return [
                    'type' => 'article',
                    'label' => 'fr' === $locale ? $article->getTitleFr() : $article->getTitleEn(),
                    'id' => $article->getId(),
                    'query' => [
                        'category' => 'fr' === $locale ? $article->getCategory()?->getSlugFr() : $article->getCategory()?->getSlugEn(),
                        'article' => 'fr' === $locale ? $article->getSlugFr() : $article->getSlugEn(),
                    ],
                    'route' => 'app_web_blog_article',
                    'link' => null,
                    'blank' => false,
                ];
            }, $this->articleRepository->findBy(['published' => true])),

            'categories' => array_map(function ($category) use ($locale) {
                return [
                    'type' => 'category',
                    'label' => 'fr' === $locale ? $category->getTitleFr() : $category->getTitleEn(),
                    'id' => $category->getId(),
                    'query' => ['category' => 'fr' === $locale ? $category->getSlugFr() : $category->getSlugEn()],
                    'route' => 'app_web_blog_category',
                    'link' => null,
                    'blank' => false,
                ];
            }, $this->categoryRepository->findAll()),

            'user' => [],
            'commerce' => [],
            'external' => [],
        ];
    }
}
