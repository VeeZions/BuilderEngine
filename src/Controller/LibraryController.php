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
use XenoLab\XenoEngine\Entity\XenoLibrary;
use XenoLab\XenoEngine\Trait\AccessTrait;

class LibraryController
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
        return new Response($this->twig->render('@XenoEngineBundle/libraries/index.html.twig'));
    }

    public function new(): Response
    {
        $this->isGranted($this->actions['new']['roles']);

        return new Response($this->twig->render('@XenoEngineBundle/libraries/new.html.twig'));
    }

    public function show(?XenoLibrary $library): Response
    {
        $this->isGranted($this->actions['show']['roles']);

        if (null === $library) {
            throw new NotFoundHttpException($this->translator->trans('error.library.not.found', [], 'XenoEngineBundle-errors'));
        }

        return new Response($this->twig->render('@XenoEngineBundle/libraries/show.html.twig'));
    }

    public function edit(?XenoLibrary $library): Response
    {
        $this->isGranted($this->actions['edit']['roles']);

        if (null === $library) {
            throw new NotFoundHttpException($this->translator->trans('error.library.not.found', [], 'XenoEngineBundle-errors'));
        }

        return new Response($this->twig->render('@XenoEngineBundle/libraries/edit.html.twig'));
    }

    public function delete(?XenoLibrary $library): Response
    {
        $this->isGranted($this->actions['delete']['roles']);

        if (null !== $library) {
            $this->entityManager->remove($library);
        }

        return new RedirectResponse($this->router->generate('xlxeb_controller_library_index'));
    }
}
