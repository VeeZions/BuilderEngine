<?php

namespace VeeZions\BuilderEngine\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Twig\Environment as TwigEnvironment;
use Symfony\Component\HttpFoundation\Response;

class FrontController
{
    public function __construct(
        private TwigEnvironment $twig,
        private Router $router,
        private EntityManagerInterface $entityManager,
    ) {
    }
    
    public function blog() :Response
    {
        return new Response($this->twig->render(
            '@BuilderEngineBundle/front/blog.html.twig',
            [
                'title' => 'Blog',
            ]
        ));
    }

    public function article(string $categorySlug, string $articleSlug) :Response
    {
        return new Response($this->twig->render(
            '@BuilderEngineBundle/front/article.html.twig',
            [
                'title' => 'Article',
            ]
        ));
    }

    public function category(string $categorySlug) :Response
    {
        return new Response($this->twig->render(
            '@BuilderEngineBundle/front/category.html.twig',
            [
                'title' => 'Catégorie',
            ]
        ));
    }
}