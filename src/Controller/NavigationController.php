<?php

namespace Vision\BuilderEngine\Controller;

use Vision\BuilderEngine\Constant\Crud\NavigationConstant;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment as TwigEnvironment;
use Vision\BuilderEngine\Entity\BuilderNavigation;
use Vision\BuilderEngine\Manager\FormManager;
use Vision\BuilderEngine\Trait\AccessTrait;
use Vision\BuilderEngine\Trait\PaginationTrait;

class NavigationController
{
    use AccessTrait;
    use PaginationTrait;

    public function __construct(
        private TwigEnvironment $twig,
        private Router $router,
        private TranslatorInterface $translator,
        private EntityManagerInterface $entityManager,
        private AuthorizationCheckerInterface $authorizationChecker,
        private FormManager $formManager,
        private NavigationConstant $constant,
        private array $actions,
    ) {
    }

    public function index(Request $request): Response
    {
        $pagination = $this->getPaginationData(
            $request,
            BuilderNavigation::class,
            $this->constant->getCrudConfig(),
            $this->entityManager
        );
        
        return new Response($this->twig->render('@BuilderEngineBundle/navigations/index.html.twig', [
            'title' => $this->formManager->translateCrudTitle('navigations', 'index'),
            'pagination' => $pagination,
        ]));
    }

    public function new(Request $request): Response
    {
        $this->isGranted($this->actions['new']['roles']);

        return new Response($this->twig->render('@BuilderEngineBundle/navigations/new-edit.html.twig', [
            'title' => $this->formManager->translateCrudTitle('navigation', 'new')
        ]));
    }

    public function show(?BuilderNavigation $navigation): Response
    {
        $this->isGranted($this->actions['show']['roles']);

        if (null === $navigation) {
            throw new NotFoundHttpException($this->translator->trans('error.navigation.not.found', [], 'BuilderEngineBundle-errors'));
        }

        return new Response($this->twig->render('@BuilderEngineBundle/navigations/show.html.twig', [
            'title' => $this->formManager->translateCrudTitle('navigation', 'show')
        ]));
    }

    public function edit(?BuilderNavigation $navigation, Request $request): Response
    {
        $this->isGranted($this->actions['edit']['roles']);

        if (null === $navigation) {
            throw new NotFoundHttpException($this->translator->trans('error.navigation.not.found', [], 'BuilderEngineBundle-errors'));
        }

        return new Response($this->twig->render('@BuilderEngineBundle/navigations/new-edit.html.twig', [
            'title' => $this->formManager->translateCrudTitle('navigation', 'edit')
        ]));
    }

    public function delete(?BuilderNavigation $navigation): Response
    {
        $this->isGranted($this->actions['delete']['roles']);

        if (null !== $navigation) {
            $this->entityManager->remove($navigation);
        }

        return new RedirectResponse($this->router->generate('xlxeb_controller_navigation_index'));
    }
}
