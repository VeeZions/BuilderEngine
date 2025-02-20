<?php

namespace XenoLab\XenoEngine\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment as TwigEnvironment;
use XenoLab\XenoEngine\Entity\XenoLibrary;

class LibraryController
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

    public function show(?XenoLibrary $library): Response
    {
        if (null === $library) {
            return new Response('oups');
        }
        return new Response('show');
    }

    public function edit(?XenoLibrary $library): Response
    {
        if (null === $library) {
            return new Response('oups');
        }
        return new Response('edit');
    }

    public function delete(?XenoLibrary $library): Response
    {
        if (null === $library) {
            return new Response('oups');
        }
        return new Response('delete');
    }
}
