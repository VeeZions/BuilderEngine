<?php

namespace XenoLab\XenoEngine\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment as TwigEnvironment;
use XenoLab\XenoEngine\Entity\XenoNavigation;

class NavigationController
{
    public function __construct(private TwigEnvironment $twig)
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

    public function show(?XenoNavigation $navigation): Response
    {
        if (null === $navigation) {
            return new Response('oups');
        }
        return new Response('show');
    }

    public function edit(?XenoNavigation $navigation): Response
    {
        if (null === $navigation) {
            return new Response('oups');
        }
        return new Response('edit');
    }

    public function delete(?XenoNavigation $navigation): Response
    {
        if (null === $navigation) {
            return new Response('oups');
        }
        return new Response('delete');
    }
}
