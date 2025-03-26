<?php

namespace VeeZions\BuilderEngine\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface as Security;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment as TwigEnvironment;
use VeeZions\BuilderEngine\Constant\NavigationConstant;
use VeeZions\BuilderEngine\Constant\TableConstant;
use VeeZions\BuilderEngine\Manager\FormManager;
use VeeZions\BuilderEngine\Provider\AuthorProvider;

readonly class EngineManager extends FormManager
{
    public function __construct(
        protected TwigEnvironment $twig,
        protected FormFactory $formFactory,
        protected RequestStack $requestStack,
        protected EntityManagerInterface $entityManager,
        protected Security $security,
        protected TranslatorInterface $translator,
        protected Router $router,
        protected AssetManager $assetManager,
        protected array $authors,
        protected array $libraryLiipFilters,
        protected array $customRoutes,
        protected TableConstant $constant,
        protected AuthorizationCheckerInterface $authorizationChecker,
        protected array $actions,
        protected AuthorProvider $authorProvider,
        protected string $formTheme,
        protected NavigationConstant $navigationConstant,
        protected ?string $localeFallback,
    )
    {

    }

    public function get(string $class): Response
    {
        return $this->provide($class);
    }

    public function new(string $class, array $twigVars = [], ?string $redirectionRoute = null): Response
    {
        $this->checkPermissions();
        return $this->engine($class, null, $twigVars, $redirectionRoute);
    }

    public function edit(string $class, ?object $data = null, array $twigVars = [], ?string $redirectionRoute = null): Response
    {
        $this->checkPermissions();
        $this->checkIfEntityExists($data, $class);
        
        return $this->engine($class, $data, $twigVars, $redirectionRoute);
    }
    
    public function delete(?object $data = null): Response
    {
        $this->checkPermissions();
        $this->checkIfEntityExists($data);
        
        return $this->remove($data);
    }
}
