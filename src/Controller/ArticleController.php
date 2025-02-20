<?php

namespace XenoLab\XenoEngine\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment as TwigEnvironment;
use XenoLab\XenoEngine\Entity\XenoArticle;

class ArticleController
{
    public function __construct(private TwigEnvironment $twig, private ?array $authors = null)
    {

    }

    public function index(): Response
    {
        return new Response('index');
    }

    public function new(): Response
    {
        return new Response('new');
    }

    public function show(?XenoArticle $article): Response
    {
        if (null === $article) {
            return new Response('oups');
        }
        return new Response('show');
    }

    public function edit(?XenoArticle $article): Response
    {
        if (null === $article) {
            return new Response('oups');
        }
        return new Response('edit');
    }

    public function delete(?XenoArticle $article): Response
    {
        if (null === $article) {
            return new Response('oups');
        }
        return new Response('delete');
    }
}
