<?php

namespace XenoLab\XenoEngine\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment as TwigEnvironment;
use XenoLab\XenoEngine\Entity\XenoNavigation;
use XenoLab\XenoEngine\Trait\AccessTrait;

class NavigationController
{
    use AccessTrait;

    public function __construct(
        private TwigEnvironment $twig,
        private Router $router,
        private TranslatorInterface $translator,
        private EntityManagerInterface $entityManager,
        private AuthorizationCheckerInterface $authorizationChecker,
        private array $actions,
    ) {
    }

    public function index(): Response
    {
        return new Response($this->twig->render('@XenoEngineBundle/navigations/index.html.twig'));
    }

    public function new(): Response
    {
        $this->isGranted($this->actions['new']['roles']);

        return new Response($this->twig->render('@XenoEngineBundle/navigations/new.html.twig'));
    }

    public function show(?XenoNavigation $navigation): Response
    {
        $this->isGranted($this->actions['show']['roles']);

        if (null === $navigation) {
            throw new NotFoundHttpException($this->translator->trans('error.navigation.not.found', [], 'XenoEngineBundle-errors'));
        }

        return new Response($this->twig->render('@XenoEngineBundle/navigations/show.html.twig'));
    }

    public function edit(?XenoNavigation $navigation): Response
    {
        $this->isGranted($this->actions['edit']['roles']);

        if (null === $navigation) {
            throw new NotFoundHttpException($this->translator->trans('error.navigation.not.found', [], 'XenoEngineBundle-errors'));
        }

        return new Response($this->twig->render('@XenoEngineBundle/navigations/edit.html.twig'));
    }

    public function delete(?XenoNavigation $navigation): Response
    {
        $this->isGranted($this->actions['delete']['roles']);

        if (null !== $navigation) {
            $this->entityManager->remove($navigation);
        }

        return new RedirectResponse($this->router->generate('xlxeb_controller_navigation_index'));
    }
}
