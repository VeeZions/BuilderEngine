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
use VeeZions\BuilderEngine\Entity\BuilderLibrary;
use VeeZions\BuilderEngine\Form\LibraryType;
use VeeZions\BuilderEngine\Manager\FormManager;
use VeeZions\BuilderEngine\Constant\ConfigConstant;

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
        
        if ($form instanceof FormView) {
            return new Response($this->twig->render(ConfigConstant::CONFIG_INTERNAL_TEMPLATE_PATH.'/libraries/index.html.twig', [
                'title' => $this->formManager->translateCrudTitle('libraries', 'index'),
                'form' => $form,
                'data' => $data,
            ])); 
        }

        return new RedirectResponse($form);
    }
}
