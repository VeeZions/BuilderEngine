<?php

namespace Vision\BuilderEngine\Controller;

use Vision\BuilderEngine\Constant\Crud\CategoryConstant;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment as TwigEnvironment;
use Vision\BuilderEngine\Entity\BuilderCategory;
use Vision\BuilderEngine\Form\CategoryType;
use Vision\BuilderEngine\Manager\FormManager;
use Vision\BuilderEngine\Trait\AccessTrait;
use Vision\BuilderEngine\Trait\PaginationTrait;

class CategoryController
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
        private CategoryConstant $constant,
        private array $actions,
    ) {
    }

    public function index(Request $request): Response
    {
        $pagination = $this->getPaginationData(
            $request,
            BuilderCategory::class,
            $this->constant->getCrudConfig(),
            $this->entityManager
        );
        
        return new Response($this->twig->render('@BuilderEngineBundle/categories/index.html.twig', [
            'title' => $this->formManager->translateCrudTitle('categories', 'index'),
            'pagination' => $pagination,
        ]));
    }

    public function new(Request $request): Response
    {
        $this->isGranted($this->actions['new']['roles']);
        $form = $this->formManager->form(CategoryType::class);

        return new Response($this->twig->render('@BuilderEngineBundle/categories/new-edit.html.twig', [
            'title' => $this->formManager->translateCrudTitle('category', 'new'),
            'form' => $form
        ]));
    }

    public function show(?BuilderCategory $category): Response
    {
        $this->isGranted($this->actions['show']['roles']);

        if (null === $category) {
            throw new NotFoundHttpException($this->translator->trans('error.category.not.found', [], 'BuilderEngineBundle-errors'));
        }

        return new Response($this->twig->render('@BuilderEngineBundle/categories/show.html.twig', [
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

        return new Response($this->twig->render('@BuilderEngineBundle/categories/new-edit.html.twig', [
            'title' => $this->formManager->translateCrudTitle('category', 'edit'),
            'form' => $form
        ]));
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
