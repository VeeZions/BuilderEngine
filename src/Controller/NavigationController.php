<?php

namespace VeeZions\BuilderEngine\Controller;

use Symfony\Component\HttpFoundation\Response;
use VeeZions\BuilderEngine\Entity\BuilderNavigation;
use VeeZions\BuilderEngine\Form\NavigationType;
use VeeZions\BuilderEngine\Manager\EngineManager;

class NavigationController
{
    public function __construct(private EngineManager $engineManager)
    {
    }

    public function index(): Response
    {
        return $this->engineManager->get(BuilderNavigation::class);
    }

    public function new(): Response
    {
        return $this->engineManager->new(NavigationType::class);
    }

    public function edit(?BuilderNavigation $article): Response
    {
        return $this->engineManager->edit(NavigationType::class, $article);
    }

    public function delete(?BuilderNavigation $article): Response
    {
        return $this->engineManager->delete($article);
    }
}
