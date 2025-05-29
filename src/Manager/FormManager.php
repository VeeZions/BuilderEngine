<?php

namespace VeeZions\BuilderEngine\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\InvalidEntityRepository;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface as Security;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment as TwigEnvironment;
use VeeZions\BuilderEngine\Constant\ConfigConstant;
use VeeZions\BuilderEngine\Constant\NavigationConstant;
use VeeZions\BuilderEngine\Constant\TableConstant;
use VeeZions\BuilderEngine\Entity\BuilderArticle;
use VeeZions\BuilderEngine\Entity\BuilderCategory;
use VeeZions\BuilderEngine\Entity\BuilderLibrary;
use VeeZions\BuilderEngine\Entity\BuilderNavigation;
use VeeZions\BuilderEngine\Entity\BuilderPage;
use VeeZions\BuilderEngine\Form\ArticleType;
use VeeZions\BuilderEngine\Form\CategoryType;
use VeeZions\BuilderEngine\Form\LibraryModalType;
use VeeZions\BuilderEngine\Form\LibrarySearchType;
use VeeZions\BuilderEngine\Form\LibraryType;
use VeeZions\BuilderEngine\Form\NavigationType;
use VeeZions\BuilderEngine\Form\PageType;
use VeeZions\BuilderEngine\Provider\AuthorProvider;
use VeeZions\BuilderEngine\Provider\LocaleProvider;
use VeeZions\BuilderEngine\Trait\AccessTrait;

readonly class FormManager
{
    use AccessTrait;

    /**
     * @param array<string, array<string, mixed>>                $authors
     * @param array<int, string>                                 $libraryLiipFilters
     * @param array<string, array<string, string>>               $customRoutes
     * @param array<string, array<string, array<string, mixed>>> $actions
     */
    public function __construct(
        protected TwigEnvironment $twig,
        protected FormFactory $formFactory,
        protected RequestStack $requestStack,
        protected EntityManagerInterface $entityManager,
        protected Security $security,
        protected TranslatorInterface $translator,
        protected Router $router,
        private GedManager $gedManager,
        private CmsManager $cmsManager,
        protected AssetManager $assetManager,
        protected array $authors,
        private string $bundleMode, /** @phpstan-ignore-line */
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
    ) {
    }

    /**
     * @param array<string, mixed> $twigVars
     */
    protected function provide(string $class, array $twigVars = []): Response
    {
        $type = $this->typeProvider($class);
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            throw new InvalidArgumentException($this::class.'::provide() expects a valid $request value');
        }
        /**@phpstan-ignore-next-line */
        $data = $this->entityManager->getRepository($class)->paginate(
            max($request->query->getInt('page', 1), 1),
            array_keys($this->constant->getColumnsFromTable($class))
        );

        return new Response($this->twig->render(
            $this->getTwigTemplate($type, 'index'),
            array_merge(['data' => $data], $twigVars)
        ));
    }

    private function typeProvider(?string $class): string
    {
        $type = match ($class) {
            BuilderArticle::class => ArticleType::class,
            BuilderPage::class => PageType::class,
            BuilderCategory::class => CategoryType::class,
            BuilderNavigation::class => NavigationType::class,
            BuilderLibrary::class => LibraryType::class,
            default => null,
        };

        if (null === $type) {
            throw new InvalidArgumentException($this::class.'::provide() expects a valid className');
        }

        return $type;
    }

    public function createLibrarySearchForm(): FormView
    {
        return $this->formFactory->create(LibrarySearchType::class, null, [
            'action' => $this->router->generate(ConfigConstant::CONFIG_MEDIA_LIST_ROUTE),
            'method' => 'GET',
            'form_theme' => $this->formTheme,
        ])->createView();
    }

    /**
     * @param array<int, string> $types
     */
    public function renderMediaList(string $order = 'asc', array $types = [], ?string $search = null, ?int $count = null): string
    {
        return $this->twig->render('@BuilderEngineInternal/libraries/list.html.twig', [
            /**@phpstan-ignore-next-line */
            'data' => $this->entityManager->getRepository(BuilderLibrary::class)->paginate($order, $types, $search, $count),
        ]);
    }

    public function renderModal(int $id): string
    {
        return $this->twig->render('@BuilderEngineInternal/libraries/modal.html.twig', [
            'media' => $this->entityManager->getRepository(BuilderLibrary::class)->find($id),
            'form' => $this->formFactory->create(LibraryModalType::class, null, [
                'action' => $this->router->generate(ConfigConstant::CONFIG_MEDIA_SAVE_ROUTE),
                'method' => 'GET',
                'form_theme' => $this->formTheme,
                'file_data' => $this->entityManager->getRepository(BuilderLibrary::class)->find($id),
            ])->createView(),
        ]);
    }

    /**
     * @param array<string, mixed> $twigVars
     */
    protected function engine(
        string $type,
        mixed $data = null,
        array $twigVars = [],
        ?string $redirectionRoute = null,
    ): Response {
        $options = $this->setOptions($type);
        $data = $this->setData($data, $type);

        $form = $this->formFactory->create($type, $data, $options);
        $form->handleRequest($this->requestStack->getCurrentRequest());

        if ($form->isSubmitted() && $form->isValid()) {
            $this->saveData($form);
            if (null === $redirectionRoute) {
                throw new InvalidArgumentException($this::class.'::engine() expects a valid $redirectionRoute value');
            }

            $route = $this->compileRedirectionRoute($redirectionRoute, $type);
            if (null === $route) {
                throw new InvalidArgumentException($this::class.'::engine() expects a valid compileRedirectionRoute() value');
            }

            return new RedirectResponse($route);
        }

        $response = $form->isSubmitted() ? 301 : 200;

        return new Response($this->twig->render(
            $this->getTwigTemplate($type, 'new-edit'),
            array_merge(['form' => $form->createView()], $twigVars)
        ), $response);
    }

    private function getTwigTemplate(string $type, string $action): string
    {
        $template = match ($type) {
            ArticleType::class => ConfigConstant::CONFIG_SHARED_TEMPLATE_PATH.'/articles/'.$action.'.html.twig',
            PageType::class => ConfigConstant::CONFIG_SHARED_TEMPLATE_PATH.'/pages/'.$action.'.html.twig',
            CategoryType::class => ConfigConstant::CONFIG_SHARED_TEMPLATE_PATH.'/categories/'.$action.'.html.twig',
            NavigationType::class => ConfigConstant::CONFIG_SHARED_TEMPLATE_PATH.'/navigations/'.$action.'.html.twig',
            LibraryType::class => ConfigConstant::CONFIG_INTERNAL_TEMPLATE_PATH.'/libraries/index.html.twig',
            default => null,
        };

        if (null === $template) {
            $formMethod = 'index' === $type ? '::provide()' : '::engine()';
            throw new InvalidArgumentException($this::class.$formMethod.' expects a valid form type');
        }

        return $template;
    }

    /**
     * @return array<string, mixed>
     */
    private function setOptions(string $type): array
    {
        $options = [];
        $authors = match ($type) {
            ArticleType::class => $this->authorProvider->provideAuthors($this->authors['articles'], 'articles'),
            PageType::class => $this->authorProvider->provideAuthors($this->authors['pages'], 'pages'),
            default => null,
        };

        if (in_array($type, [ArticleType::class, PageType::class], true)) {
            $options['authors'] = $authors;
            $token = $this->security->getToken();
            $options['user_id'] = null !== $token ? $token->getUser() : null;
        }

        if (in_array($type, [ArticleType::class, PageType::class, NavigationType::class, CategoryType::class], true)) {
            $options['locale_fallback'] = $this->localeFallback;
        }

        $listUrl = match ($type) {
            ArticleType::class => $this->customRoutes['articles_routes']['list'],
            PageType::class => $this->customRoutes['pages_routes']['list'],
            CategoryType::class => $this->customRoutes['categories_routes']['list'],
            NavigationType::class => $this->customRoutes['navigations_routes']['list'],
            LibraryType::class => ConfigConstant::CONFIG_DEFAULT_ROUTES['libraries_routes']['list'],
            default => null,
        };

        if (null === $listUrl) {
            throw new InvalidArgumentException($this::class.'::engine() expects a valid $type value');
        }

        if (NavigationType::class === $type) {
            $options['navigation_types'] = array_flip($this->navigationConstant->getTypes());
        }

        if (LibraryType::class === $type) {
            $options['error_extensions_message'] = $this->translator->trans('error.label.libraries.mime_extensions_message', [], 'BuilderEngineBundle-errors');
            $options['error_max_size_message'] = $this->translator->trans('error.label.libraries.max_size_message', [], 'BuilderEngineBundle-errors');
            $options['max_upload_size'] = $this->maxUploadFile;
        }

        $options['list_url'] = $this->router->generate($listUrl);
        $options['message'] = $this->translator->trans('form.message.back.list', [], 'BuilderEngineBundle-forms');
        $options['form_theme'] = $this->formTheme;

        return $options;
    }

    private function setData(mixed $data, string $type): mixed
    {
        if (null === $data) {
            $data = match ($type) {
                ArticleType::class => new BuilderArticle(),
                PageType::class => new BuilderPage(),
                CategoryType::class => new BuilderCategory(),
                NavigationType::class => new BuilderNavigation(),
                LibraryType::class => null,
                default => null,
            };
        }

        if (null === $data && LibraryType::class !== $type) {
            throw new InvalidArgumentException($this::class.'::engine() expects a valid form type');
        }

        return $data;
    }

    private function saveData(FormInterface $form): void
    {
        $innerType = $form->getConfig()->getType()->getInnerType();
        $entity = $form->getData();
        $entity = match (true) {
            $entity instanceof BuilderNavigation => $this->prepareNavigationData($form, $entity),
            $entity instanceof BuilderPage, $entity instanceof BuilderArticle, $entity instanceof BuilderCategory => $this->prepareCmsEntitiesData($form, $entity),
            $innerType instanceof LibraryType => $this->assetManager->upload(
                $form->get('file')->getData(),
                $this->libraryLiipFilters
            ),
            default => null,
        };

        if (!$innerType instanceof LibraryType) {
            if (is_object($entity) && null !== $entity->getId()) {
                $this->entityManager->persist($entity);
            }
            $this->entityManager->flush();
            $this->callBackActions($entity);
        }
    }

    private function compileRedirectionRoute(string $redirectionRoute, string $type): ?string
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            throw new InvalidArgumentException($this::class.'::compileRedirectionRoute() expects a valid $request value');
        }

        if ($this->isReloaded($request->request->all())) {
            return $request->headers->get('referer');
        }

        $routeToRedirect = match ($type) {
            ArticleType::class => $this->customRoutes['articles_routes']['list'],
            PageType::class => $this->customRoutes['pages_routes']['list'],
            CategoryType::class => $this->customRoutes['categories_routes']['list'],
            NavigationType::class => $this->customRoutes['navigations_routes']['list'],
            LibraryType::class => ConfigConstant::CONFIG_DEFAULT_ROUTES['libraries_routes']['list'],
            default => null,
        };

        if (null === $routeToRedirect) {
            throw new InvalidArgumentException($this::class.'::engine() expects a valid redirection route');
        }

        return $redirectionRoute ?
            $this->router->generate($redirectionRoute) :
            $this->router->generate($routeToRedirect);
    }

    /**
     * @param array<string, mixed> $request
     */
    private function isReloaded(array $request): bool
    {
        foreach ($request as $row) {
            return is_array($row) && isset($row['buttons']['save_and_stay']);
        }

        return false;
    }

    /**
     * @return array<string, array<string, string>>
     */
    public function getAvailableLocales(): array
    {
        return $this->localeProvider->getList();
    }

    public function translateCrudTitle(string $entity, string $type): string
    {
        if ('libraries' === $entity && 'index' === $type) {
            return $this->translator->trans('form.media.library', [], 'BuilderEngineBundle-forms');
        }

        return $this->translator->trans(
            'form.title.'.$type,
            ['%entity%' => $this->translator->trans(
                'form.entities.'.$entity,
                [],
                'BuilderEngineBundle-forms'
            )],
            'BuilderEngineBundle-forms'
        );
    }

    private function callBackActions(mixed $entity): void
    {
        if ($entity instanceof BuilderPage) {
            $isEdition = null !== $entity->getId();
            $this->gedManager->setGedsFromPageBuilder($entity);
            if ($isEdition) {
                $this->cmsManager->createPageFile($entity);
            }
        }
    }

    private function prepareNavigationData(FormInterface $form, BuilderNavigation $entity): BuilderNavigation
    {
        $data = $form->getData();
        if (!is_array($data)) {
            throw new InvalidArgumentException($this::class.'::prepareNavigationData() expects a valid $data array');
        }
        $json = json_decode($data['data'], true);
        $json = is_array($json) ? $json : [];
        $entity->setContent($json);

        return $entity;
    }

    private function prepareCmsEntitiesData(
        FormInterface $form,
        BuilderPage|BuilderArticle|BuilderCategory $entity,
    ): BuilderPage|BuilderArticle|BuilderCategory {
        $isEdition = null !== $entity->getId();
        $newLibraryId = $form->get('libraries')->getData();

        if ($isEdition) {
            $oldLibrary = $entity->getLibraries()->first();
            if ($oldLibrary instanceof BuilderLibrary) {
                $entity->removeLibrary($oldLibrary);
            }
        }

        if (null !== $newLibraryId) {
            $newLibrary = $this->entityManager->getRepository(BuilderLibrary::class)->find($newLibraryId);
            if (null !== $newLibrary) {
                $entity->addLibrary($newLibrary);
            }
        }

        return $entity;
    }

    protected function checkIfEntityExists(?object $data = null, ?string $class = null): void
    {
        if (null === $data) {
            $entity = match ($class) {
                BuilderArticle::class => 'article',
                BuilderCategory::class => 'category',
                BuilderPage::class => 'page',
                BuilderNavigation::class => 'navigation',
                default => 'entity',
            };

            throw new InvalidEntityRepository($this->translator->trans('error.'.$entity.'.not.found', [], 'BuilderEngineBundle-errors'));
        }
    }

    protected function remove(?object $data = null): Response
    {
        $this->checkIfEntityExists($data);

        $routeToRedirect = match (true) {
            $data instanceof BuilderArticle => $this->customRoutes['articles_routes']['list'],
            $data instanceof BuilderPage => $this->customRoutes['pages_routes']['list'],
            $data instanceof BuilderCategory => $this->customRoutes['categories_routes']['list'],
            $data instanceof BuilderNavigation => $this->customRoutes['navigations_routes']['list'],
            $data instanceof BuilderLibrary => ConfigConstant::CONFIG_DEFAULT_ROUTES['libraries_routes']['list'],
            default => null,
        };

        if (null === $routeToRedirect) {
            throw new InvalidArgumentException($this::class.'::remove() expects a valid redirection route');
        }

        if (null !== $data) {
            $this->entityManager->remove($data);
            $this->entityManager->flush();
        }

        return new RedirectResponse($this->router->generate($routeToRedirect));
    }

    protected function checkPermissions(): void
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            throw new InvalidArgumentException($this::class.'::checkPermissions() expects a valid $request value');
        }
        $currentRoute = $request->attributes->get('_route');

        foreach ($this->customRoutes as $entity => $routes) {
            foreach ($routes as $action => $route) {
                if ($currentRoute === $route) {
                    $index = str_replace('_routes', '', $entity);
                    if (!is_array($this->actions[$index][$action]['roles'])) {
                        throw new InvalidArgumentException($this::class.'::checkPermissions() expects a valid $roles array');
                    }
                    $this->isGranted($this->actions[$index][$action]['roles']);
                }
            }
        }
    }

    /**
     * @param array<string, array<string, string>> $flags
     */
    public function getFlagFromLocale(string $locale, array $flags): ?string
    {
        if (isset($flags[$locale])) {
            $data = $flags[$locale];

            return sprintf('<span title="%s">%s</span>', $data['language'], $data['flag']);
        }

        return null;
    }
}
