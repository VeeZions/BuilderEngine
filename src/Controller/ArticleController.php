<?php

namespace VeeZions\BuilderEngine\Controller;

use Symfony\Component\HttpFoundation\Response;
use VeeZions\BuilderEngine\Entity\BuilderArticle;
use VeeZions\BuilderEngine\Form\ArticleType;
use VeeZions\BuilderEngine\Manager\EngineManager;

class ArticleController
{
    public function __construct(private EngineManager $engineManager)
    {
    }

    public function index(): Response
    {
        return $this->engineManager->get(BuilderArticle::class);
    }

    public function new(): Response
    {
        return $this->engineManager->new(ArticleType::class);
    }

    public function edit(?BuilderArticle $article): Response
    {
        return $this->engineManager->edit(ArticleType::class, $article);
    }

    public function delete(?BuilderArticle $article): Response
    {
        return $this->engineManager->delete($article);
    }
}
