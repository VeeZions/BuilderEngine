<?php

namespace VeeZions\BuilderEngine\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Twig\Environment as TwigEnvironment;
use VeeZions\BuilderEngine\Entity\BuilderArticle;
use VeeZions\BuilderEngine\Entity\BuilderCategory;

class FrontController
{
    public function __construct(
        private readonly TwigEnvironment        $twig,
        private readonly EntityManagerInterface $entityManager,
        private readonly RequestStack           $requestStack,
    ) {
    }

    public function blog(): Response
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            throw new BadRequestHttpException('Request is null');
        }

        $locale = $request->getLocale();
        $categories = $this->entityManager->getRepository(BuilderCategory::class)->findBy(['locale' => $locale]);
        $articles = $this->entityManager->getRepository(BuilderArticle::class)->findBy(['locale' => $locale, 'published' => true], ['publishedAt' => 'DESC']);

        return new Response($this->twig->render(
            '@BuilderEngineBundle/front/blog.html.twig',
            [
                'articles' => $articles,
                'categories' => $categories,
                'title' => 'Blog',
            ]
        ));
    }

    public function article(string $slug): Response
    {
        return new Response($this->twig->render(
            '@BuilderEngineBundle/front/article.html.twig',
            [
                'title' => 'Article',
            ]
        ));
    }

    public function category(string $slug): Response
    {
        return new Response($this->twig->render(
            '@BuilderEngineBundle/front/category.html.twig',
            [
                'title' => 'Catégorie',
            ]
        ));
    }
}
