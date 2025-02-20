<?php

namespace XenoLab\XenoEngine\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment as TwigEnvironment;
use XenoLab\XenoEngine\Entity\XenoCategory;

class CategoryController
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

    public function show(?XenoCategory $category): Response
    {
        if (null === $category) {
            return new Response('oups');
        }
        return new Response('show');
    }

    public function edit(?XenoCategory $category): Response
    {
        if (null === $category) {
            return new Response('oups');
        }
        return new Response('edit');
    }

    public function delete(?XenoCategory $category): Response
    {
        if (null === $category) {
            return new Response('oups');
        }
        return new Response('delete');
    }
}
