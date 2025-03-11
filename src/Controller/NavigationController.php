<?php

namespace VeeZions\BuilderEngine\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment as TwigEnvironment;
use VeeZions\BuilderEngine\Constant\TableConstant;
use VeeZions\BuilderEngine\Entity\BuilderNavigation;
use VeeZions\BuilderEngine\Form\NavigationType;
use VeeZions\BuilderEngine\Manager\FormManager;
use VeeZions\BuilderEngine\Trait\AccessTrait;
use VeeZions\BuilderEngine\Constant\ConfigConstant;

class NavigationController
{
    use AccessTrait;

    public function __construct(
        private TwigEnvironment $twig,
        private Router $router,
        private TranslatorInterface $translator,
        private EntityManagerInterface $entityManager,
        private AuthorizationCheckerInterface $authorizationChecker,
        private FormManager $formManager,
        private TableConstant $constant,
        private array $actions,
    ) {
    }

    public function index(Request $request): Response
    {
        $data = $this->entityManager->getRepository(BuilderNavigation::class)->paginate(
            max($request->query->getInt('page', 1), 1),
            array_keys($this->constant->getColumnsFromTable(BuilderNavigation::class))
        );
        
        return new Response($this->twig->render(ConfigConstant::CONFIG_SHARED_TEMPLATE_PATH.'/navigations/index.html.twig', [
            'title' => $this->formManager->translateCrudTitle('navigations', 'index'),
            'data' => $data,
        ]));
    }

    public function new(Request $request): Response
    {
        $this->isGranted($this->actions['new']['roles']);
        $form = $this->formManager->form(NavigationType::class);

        if ($form instanceof FormView) {
            return new Response($this->twig->render(ConfigConstant::CONFIG_SHARED_TEMPLATE_PATH.'/navigations/new-edit.html.twig', [
                'title' => $this->formManager->translateCrudTitle('navigation', 'new'),
                'form' => $form
            ]));
        }
        
        return new RedirectResponse($form);
    }

    public function show(?BuilderNavigation $navigation): Response
    {
        $this->isGranted($this->actions['show']['roles']);

        if (null === $navigation) {
            throw new NotFoundHttpException($this->translator->trans('error.navigation.not.found', [], 'BuilderEngineBundle-errors'));
        }

        return new Response($this->twig->render(ConfigConstant::CONFIG_SHARED_TEMPLATE_PATH.'/navigations/show.html.twig', [
            'title' => $this->formManager->translateCrudTitle('navigation', 'show')
        ]));
    }

    public function edit(?BuilderNavigation $navigation, Request $request): Response
    {
        $this->isGranted($this->actions['edit']['roles']);

        if (null === $navigation) {
            throw new NotFoundHttpException($this->translator->trans('error.navigation.not.found', [], 'BuilderEngineBundle-errors'));
        }
        $form = $this->formManager->form(NavigationType::class, $navigation);

        if ($form instanceof FormView) {
            return new Response($this->twig->render(ConfigConstant::CONFIG_SHARED_TEMPLATE_PATH.'/navigations/new-edit.html.twig', [
                'title' => $this->formManager->translateCrudTitle('navigation', 'edit'),
                'form' => $form
            ]));
        }
        
        return new RedirectResponse($form);
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
