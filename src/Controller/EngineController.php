<?php

namespace XenoLabs\XenoEngine\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Twig\Environment as TwigEnvironment;

class EngineController
{
    public function __construct(private TwigEnvironment $twig)
    {

    }

    public function ged(): Response
    {
        return new Response('Salut');
    }
}
