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
use VeeZions\BuilderEngine\Entity\BuilderArticle;
use VeeZions\BuilderEngine\Form\ArticleType;
use VeeZions\BuilderEngine\Manager\FormManager;
use VeeZions\BuilderEngine\Trait\AccessTrait;
use VeeZions\BuilderEngine\Constant\ConfigConstant;

class ArticleController
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
        $data = $this->entityManager->getRepository(BuilderArticle::class)->paginate(
            max($request->query->getInt('page', 1), 1),
            array_keys($this->constant->getColumnsFromTable(BuilderArticle::class))
        );

        return new Response($this->twig->render(ConfigConstant::CONFIG_SHARED_TEMPLATE_PATH.'/articles/index.html.twig', [
            'title' => $this->formManager->translateCrudTitle('articles', 'index'),
            'data' => $data,
        ]));
    }

    public function new(Request $request): Response
    {
        $this->isGranted($this->actions['new']['roles']);
        $form = $this->formManager->form(ArticleType::class);

        if ($form instanceof FormView) {
            return new Response($this->twig->render(ConfigConstant::CONFIG_SHARED_TEMPLATE_PATH.'/articles/new-edit.html.twig', [
                'title' => $this->formManager->translateCrudTitle('article', 'new'),
                'form' => $form,
            ]));
        }

        return new RedirectResponse($form);
    }

    public function show(?BuilderArticle $article): Response
    {
        $this->isGranted($this->actions['show']['roles']);

        if (null === $article) {
            throw new NotFoundHttpException($this->translator->trans('error.article.not.found', [], 'BuilderEngineBundle-errors'));
        }

        return new Response($this->twig->render(ConfigConstant::CONFIG_SHARED_TEMPLATE_PATH.'/articles/show.html.twig', [
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

        if ($form instanceof FormView) {
            return new Response($this->twig->render(ConfigConstant::CONFIG_SHARED_TEMPLATE_PATH.'/articles/new-edit.html.twig', [
                'title' => $this->formManager->translateCrudTitle('article', 'edit'),
                'form' => $form,
            ]));
        }

        return new RedirectResponse($form);
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
