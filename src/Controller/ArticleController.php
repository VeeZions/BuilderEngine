<?php

namespace Vision\BuilderEngine\Controller;

use Vision\BuilderEngine\Constant\Crud\ArticleConstant;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment as TwigEnvironment;
use Vision\BuilderEngine\Entity\BuilderArticle;
use Vision\BuilderEngine\Form\ArticleType;
use Vision\BuilderEngine\Manager\FormManager;
use Vision\BuilderEngine\Trait\AccessTrait;
use Vision\BuilderEngine\Trait\PaginationTrait;

class ArticleController
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
        private ArticleConstant $constant,
        private array $actions,
    ) {
    }

    public function index(Request $request): Response
    {
        $pagination = $this->getPaginationData(
            $request,
            BuilderArticle::class,
            $this->constant->getCrudConfig(),
            $this->entityManager
        );

        return new Response($this->twig->render('@BuilderEngineBundle/articles/index.html.twig', [
            'title' => $this->formManager->translateCrudTitle('articles', 'index'),
            'pagination' => $pagination,
        ]));
    }

    public function new(Request $request): Response
    {
        $this->isGranted($this->actions['new']['roles']);
        $form = $this->formManager->form(ArticleType::class);

        return new Response($this->twig->render('@BuilderEngineBundle/articles/new-edit.html.twig', [
            'title' => $this->formManager->translateCrudTitle('article', 'new'),
            'form' => $form,
        ]));
    }

    public function show(?BuilderArticle $article): Response
    {
        $this->isGranted($this->actions['show']['roles']);

        if (null === $article) {
            throw new NotFoundHttpException($this->translator->trans('error.article.not.found', [], 'BuilderEngineBundle-errors'));
        }

        return new Response($this->twig->render('@BuilderEngineBundle/articles/show.html.twig', [
            'title' => $this->formManager->translateCrudTitle('article', 'show'),
        ]));
    }

    public function edit(?BuilderArticle $article, Request $request): Response
    {
        $this->isGranted($this->actions['edit']['roles']);

        if (null === $article) {
            throw new NotFoundHttpException($this->translator->trans('error.article.not.found', [], 'BuilderEngineBundle-errors'));
        }
        $form = $this->formManager->form(ArticleType::class, $article);

        return new Response($this->twig->render('@BuilderEngineBundle/articles/new-edit.html.twig', [
            'title' => $this->formManager->translateCrudTitle('article', 'edit'),
            'form' => [],
        ]));
    }

    public function delete(?BuilderArticle $article): Response
    {
        $this->isGranted($this->actions['delete']['roles']);

        if (null !== $article) {
            $this->entityManager->remove($article);
        }

        return new RedirectResponse($this->router->generate('xlxeb_controller_article_index'));
    }
}
