<?php

namespace Vision\BuilderEngine\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment as TwigEnvironment;
use Vision\BuilderEngine\Entity\BuilderLibrary;
use Vision\BuilderEngine\Form\LibraryType;
use Vision\BuilderEngine\Manager\FormManager;

class LibraryController
{
    public function __construct(
        private TwigEnvironment $twig,
        private TranslatorInterface $translator,
        private EntityManagerInterface $entityManager,
        private FormManager $formManager
    ) {
    }

    public function index(): Response
    {
        $form = $this->formManager->form(LibraryType::class);
        $data = $this->entityManager->getRepository(BuilderLibrary::class)->paginate();

        return new Response($this->twig->render('@BuilderEngineInternal/libraries/index.html.twig', [
            'title' => $this->formManager->translateCrudTitle('libraries', 'index'),
            'form' => $form,
            'data' => $data,
        ]));
    }
}
