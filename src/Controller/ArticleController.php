<?php

namespace XenoLab\XenoEngine\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment as TwigEnvironment;
use XenoLab\XenoEngine\Entity\XenoArticle;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use XenoLab\XenoEngine\Trait\AccessTrait;


class ArticleController
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
        return new Response($this->twig->render('@XenoEngineBundle/articles/index.html.twig'));
    }

    public function new(): Response
    {
        $this->isGranted($this->actions['new']['roles']);

        return new Response($this->twig->render('@XenoEngineBundle/articles/new.html.twig', [
            'authors' => $this->authors,
        ]));
    }

    public function show(?XenoArticle $article): Response
    {
        $this->isGranted($this->actions['show']['roles']);

        if (null === $article) {
            throw new NotFoundHttpException($this->translator->trans(
                'error.article.not.found',
                [],
                'XenoEngineBundle-errors'
            ));
        }

        return new Response($this->twig->render('@XenoEngineBundle/articles/show.html.twig'));
    }

    public function edit(?XenoArticle $article): Response
    {
        $this->isGranted($this->actions['edit']['roles']);

        if (null === $article) {
            throw new NotFoundHttpException($this->translator->trans(
                'error.article.not.found',
                [],
                'XenoEngineBundle-errors'
            ));
        }

        return new Response($this->twig->render('@XenoEngineBundle/articles/edit.html.twig', [
            'authors' => $this->authors,
        ]));
    }

    public function delete(?XenoArticle $article): Response
    {
        $this->isGranted($this->actions['delete']['roles']);

        if (null !== $article) {
            $this->entityManager->remove($article);
        }

        return new RedirectResponse($this->router->generate('xlxeb_controller_article_index'));
    }
}
