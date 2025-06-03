<?php

namespace VeeZions\BuilderEngine\Controller;

use Symfony\Component\HttpFoundation\Response;
use VeeZions\BuilderEngine\Entity\BuilderPage;
use VeeZions\BuilderEngine\Form\PageType;
use VeeZions\BuilderEngine\Manager\EngineManager;

class PageController
{
    public function __construct(private readonly EngineManager $engineManager)
    {
    }

    public function index(): Response
    {
        return $this->engineManager->get(BuilderPage::class);
    }

    public function new(): Response
    {
        return $this->engineManager->new(PageType::class);
    }

    public function edit(?BuilderPage $article): Response
    {
        return $this->engineManager->edit(PageType::class, $article);
    }

    public function delete(?BuilderPage $article): Response
    {
        return $this->engineManager->delete($article);
    }
}
