<?php

namespace VeeZions\BuilderEngine\Controller;

use Symfony\Component\HttpFoundation\Response;
use VeeZions\BuilderEngine\Constant\ConfigConstant;
use VeeZions\BuilderEngine\Manager\EngineManager;

class FrontController
{
    public function __construct(
        private readonly EngineManager $engineManager,
    ) {
    }

    public function blog(): Response
    {
        return $this->engineManager->frontRender(ConfigConstant::CONFIG_RENDER_BLOG);
    }

    public function article(string $slug): Response
    {
        return $this->engineManager->frontRender(ConfigConstant::CONFIG_RENDER_ARTICLE, $slug);
    }

    public function category(string $slug): Response
    {
        return $this->engineManager->frontRender(ConfigConstant::CONFIG_RENDER_CATEGORY, $slug);
    }
}
