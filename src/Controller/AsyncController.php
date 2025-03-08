<?php

namespace Vision\BuilderEngine\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment as TwigEnvironment;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class AsyncController
{
    private array $slugs = ['mon-test' => 'test'];

    public function __construct(
        private TwigEnvironment $twig,
        private Router $router,
        private TranslatorInterface $translator,
        private EntityManagerInterface $entityManager,
        private AuthorizationCheckerInterface $authorizationChecker,
    ) {
    }

    public function index(Request $request, string $slug): Response
    {
        if (!$request->isXmlHttpRequest() || !$request->isMethod('POST') || !isset($this->slugs[$slug])) {
            throw new NotFoundHttpException($this->translator->trans('error.async.not.found', [], 'BuilderEngineBundle-errors'));
        }
        $classMethod = $this->slugs[$slug];
        return new JsonResponse(['data' => $this->$classMethod($request)]);
    }

    private function test(Request $request): string
    {
        return 'test';
    }
}
