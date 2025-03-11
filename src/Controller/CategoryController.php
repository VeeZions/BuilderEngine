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
use VeeZions\BuilderEngine\Entity\BuilderCategory;
use VeeZions\BuilderEngine\Form\CategoryType;
use VeeZions\BuilderEngine\Manager\FormManager;
use VeeZions\BuilderEngine\Trait\AccessTrait;
use VeeZions\BuilderEngine\Constant\ConfigConstant;

class CategoryController
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
        $data = $this->entityManager->getRepository(BuilderCategory::class)->paginate(
            max($request->query->getInt('page', 1), 1), 
            array_keys($this->constant->getColumnsFromTable(BuilderCategory::class))
        );
        
        return new Response($this->twig->render(ConfigConstant::CONFIG_SHARED_TEMPLATE_PATH.'/categories/index.html.twig', [
            'title' => $this->formManager->translateCrudTitle('categories', 'index'),
            'data' => $data,
        ]));
    }

    public function new(Request $request): Response
    {
        $this->isGranted($this->actions['new']['roles']);
        $form = $this->formManager->form(CategoryType::class);

        if ($form instanceof FormView) {
            return new Response($this->twig->render(ConfigConstant::CONFIG_SHARED_TEMPLATE_PATH.'/categories/new-edit.html.twig', [
                'title' => $this->formManager->translateCrudTitle('category', 'new'),
                'form' => $form
            ]));
        }
        
        return new RedirectResponse($form);
    }

    public function show(?BuilderCategory $category): Response
    {
        $this->isGranted($this->actions['show']['roles']);

        if (null === $category) {
            throw new NotFoundHttpException($this->translator->trans('error.category.not.found', [], 'BuilderEngineBundle-errors'));
        }

        return new Response($this->twig->render(ConfigConstant::CONFIG_SHARED_TEMPLATE_PATH.'/categories/show.html.twig', [
            'title' => $this->formManager->translateCrudTitle('category', 'show')
        ]));
    }

    public function edit(?BuilderCategory $category, Request $request): Response
    {
        $this->isGranted($this->actions['edit']['roles']);

        if (null === $category) {
            throw new NotFoundHttpException($this->translator->trans('error.category.not.found', [], 'BuilderEngineBundle-errors'));
        }
        $form = $this->formManager->form(CategoryType::class, $category);

        if ($form instanceof FormView) {
            return new Response($this->twig->render(ConfigConstant::CONFIG_SHARED_TEMPLATE_PATH.'/categories/new-edit.html.twig', [
                'title' => $this->formManager->translateCrudTitle('category', 'edit'),
                'form' => $form
            ]));
        }
        
        return new RedirectResponse($form);
    }

    public function delete(?BuilderCategory $category): Response
    {
        $this->isGranted($this->actions['delete']['roles']);

        if (null !== $category) {
            $this->entityManager->remove($category);
        }

        return new RedirectResponse($this->router->generate('xlxeb_controller_category_index'));
    }
}
