<?php

namespace XenoLab\XenoEngine\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment as TwigEnvironment;
use XenoLab\XenoEngine\Entity\XenoPage;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use XenoLab\XenoEngine\Trait\AccessTrait;

class PageController
{
    use AccessTrait;

    public function __construct(
        private TwigEnvironment $twig,
        private Router $router,
        private TranslatorInterface $translator,
        private EntityManagerInterface $entityManager,
        private AuthorizationCheckerInterface $authorizationChecker,
        private array $authors,
        private array $actions,
    )
    {

    }

    public function index(): Response
    {
        return new Response($this->twig->render('@XenoEngineBundle/pages/index.html.twig'));
    }

    public function new(): Response
    {
        $this->isGranted($this->actions['new']['roles']);

        return new Response($this->twig->render('@XenoEngineBundle/pages/new.html.twig'));
    }

    public function show(?XenoPage $page): Response
    {
        $this->isGranted($this->actions['show']['roles']);

        if (null === $page) {
            throw new NotFoundHttpException($this->translator->trans(
                'error.page.not.found',
                [],
                'XenoEngineBundle-errors'
            ));
        }

        return new Response($this->twig->render('@XenoEngineBundle/pages/show.html.twig'));
    }

    public function edit(?XenoPage $page): Response
    {
        $this->isGranted($this->actions['edit']['roles']);

        if (null === $page) {
            throw new NotFoundHttpException($this->translator->trans(
                'error.page.not.found',
                [],
                'XenoEngineBundle-errors'
            ));
        }

        return new Response($this->twig->render('@XenoEngineBundle/pages/edit.html.twig'));
    }

    public function delete(?XenoPage $page): Response
    {
        $this->isGranted($this->actions['delete']['roles']);

        if (null !== $page) {
            $this->entityManager->remove($page);
        }

        return new RedirectResponse($this->router->generate('xlxeb_controller_page_index'));
    }
}
