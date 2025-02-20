<?php

namespace XenoLab\XenoEngine\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment as TwigEnvironment;

class AsyncController
{
    public function __construct(private TwigEnvironment $twig)
    {
        
    }
    
    public function index(): Response
    {
        return new Response('index');
    }
}
