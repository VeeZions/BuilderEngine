<?php

namespace XenoLab\XenoEngine\Controller;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Twig\Environment as TwigEnvironment;

class EngineController
{
    public function __construct(private TwigEnvironment $twig, private ?int $clientId = null)
    {

    }

    public function ged(): Response
    {
        return new Response(
            $this->twig->render(
                '@!XenoEngine/test.html.twig',
                [
                    'clientId' => $this->clientId
                ]
            )
        );
    }
}
