<?php

namespace VeeZions\BuilderEngine\Controller;

use Symfony\Component\HttpFoundation\Response;
use VeeZions\BuilderEngine\Entity\BuilderCategory;
use VeeZions\BuilderEngine\Form\CategoryType;
use VeeZions\BuilderEngine\Manager\EngineManager;

class CategoryController
{
    public function __construct(private EngineManager $engineManager)
    {
        
    }

    public function index(): Response
    {
        return $this->engineManager->get(BuilderCategory::class);
    }

    public function new(): Response
    {
        return $this->engineManager->new(CategoryType::class);
    }

    public function edit(?BuilderCategory $article): Response
    {
        return $this->engineManager->edit(CategoryType::class, $article);
    }

    public function delete(?BuilderCategory $article): Response
    {
        return $this->engineManager->delete($article);
    }
}
