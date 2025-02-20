<?php

namespace XenoLab\XenoEngine\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twig\Environment as TwigEnvironment;
use XenoLab\XenoEngine\Entity\XenoCategory;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use XenoLab\XenoEngine\Trait\AccessTrait;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class CategoryController
{
    use AccessTrait;

    public function __construct(
        private TwigEnvironment $twig,
        private Router $router,
        private TranslatorInterface $translator,
        private EntityManagerInterface $entityManager,
        private AuthorizationCheckerInterface $authorizationChecker,
        private array $actions,
    )
    {

    }

    public function index(): Response
    {
        return new Response($this->twig->render('@XenoEngineBundle/categories/index.html.twig'));
    }

    public function new(): Response
    {
        $this->isGranted($this->actions['new']['roles']);

        return new Response($this->twig->render('@XenoEngineBundle/categories/new.html.twig'));
    }

    public function show(?XenoCategory $category): Response
    {
        $this->isGranted($this->actions['show']['roles']);

        if (null === $category) {
            throw new NotFoundHttpException($this->translator->trans(
                'error.category.not.found',
                [],
                'XenoEngineBundle-errors'
            ));
        }

        return new Response($this->twig->render('@XenoEngineBundle/categories/show.html.twig'));
    }

    public function edit(?XenoCategory $category): Response
    {
        $this->isGranted($this->actions['edit']['roles']);

        if (null === $category) {
            throw new NotFoundHttpException($this->translator->trans(
                'error.category.not.found',
                [],
                'XenoEngineBundle-errors'
            ));
        }

        return new Response($this->twig->render('@XenoEngineBundle/categories/edit.html.twig'));
    }

    public function delete(?XenoCategory $category): Response
    {
        $this->isGranted($this->actions['delete']['roles']);

        if (null !== $category) {
            $this->entityManager->remove($category);
        }
        
        return new RedirectResponse($this->router->generate('xlxeb_controller_category_index'));
    }
}
