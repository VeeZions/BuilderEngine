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
use VeeZions\BuilderEngine\Provider\AuthorProvider;
use VeeZions\BuilderEngine\Provider\LocaleProvider;

readonly class EngineManager extends FormManager
{
    /**
     * @param array<string, array<string, mixed>>                $authors
     * @param array<int, string>                                 $libraryLiipFilters
     * @param array<string, array<string, string>>               $customRoutes
     * @param array<string, array<string, array<string, mixed>>> $actions
     * @param array<string, string>                              $frontRoutes
     */
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
        protected LocaleProvider $localeProvider,
        protected AuthorProvider $authorProvider,
        protected string $formTheme,
        protected NavigationConstant $navigationConstant,
        protected ?string $localeFallback,
        protected string $maxUploadFile,
        protected array $frontRoutes,
    ) {
    }

    public function get(string $class): Response
    {
        return $this->provide($class);
    }

    /**
     * @param array<string, mixed> $twigVars
     */
    public function new(string $class, array $twigVars = [], ?string $redirectionRoute = null): Response
    {
        $this->checkPermissions();

        return $this->engine($class, null, $twigVars, $redirectionRoute);
    }

    /**
     * @param array<string, mixed> $twigVars
     */
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
