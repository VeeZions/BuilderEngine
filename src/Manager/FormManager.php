<?php

namespace VeeZions\BuilderEngine\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\InvalidEntityRepository;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Intl\Locales;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment as TwigEnvironment;
use VeeZions\BuilderEngine\Constant\AssetConstant;
use VeeZions\BuilderEngine\Constant\ConfigConstant;
use VeeZions\BuilderEngine\Entity\BuilderArticle;
use VeeZions\BuilderEngine\Entity\BuilderPage;
use VeeZions\BuilderEngine\Entity\BuilderCategory;
use VeeZions\BuilderEngine\Entity\BuilderLibrary;
use VeeZions\BuilderEngine\Entity\BuilderNavigation;
use VeeZions\BuilderEngine\Form\ArticleType;
use VeeZions\BuilderEngine\Form\CategoryType;
use VeeZions\BuilderEngine\Form\LibraryType;
use VeeZions\BuilderEngine\Form\NavigationType;
use VeeZions\BuilderEngine\Form\PageType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface as Security;
use VeeZions\BuilderEngine\Constant\TableConstant;
use VeeZions\BuilderEngine\Trait\AccessTrait;
use VeeZions\BuilderEngine\Provider\LocaleProvider;

readonly class FormManager
{
    use AccessTrait;

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
        private AssetManager $assetManager,
        protected array $authors,
        private string $bundleMode,
        private array $libraryLiipFilters,
        protected array $customRoutes,
        protected TableConstant $constant,
        protected AuthorizationCheckerInterface $authorizationChecker,
        protected array $actions,
        protected LocaleProvider $localeProvider,
    )
    {
        
    }
    
    protected function provide(string $class, array $twigVars = []): Response
    {
        $type = $this->typeProvider($class);
        $data = $this->entityManager->getRepository($class)->paginate(
            max($this->requestStack->getCurrentRequest()->query->getInt('page', 1), 1),
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
            throw new InvalidArgumentException($this::class . '::provide() expects a valid className');
        }

        return $type;
    }
    
    protected function engine(
        string $type, 
        mixed $data = null,
        array $twigVars = [],
        ?string $redirectionRoute = null,
    ): Response
    {
        $options = $this->setOptions($type);
        $data = $this->setData($data, $type);
        
        $form = $this->formFactory->create($type, $data, $options);
        $form->handleRequest($this->requestStack->getCurrentRequest());
        
        if ($form->isSubmitted() && $form->isValid()) {

            $this->saveData($form);
            return new RedirectResponse($this->compileRedirectionRoute($redirectionRoute, $type));
        }

        return new Response($this->twig->render(
            $this->getTwigTemplate($type, 'new-edit'),
            array_merge(['form' => $form->createView()], $twigVars)
        ));
    }
    
    private function getTwigTemplate(string $type, string $action): string
    {
        return match ($type) {
            ArticleType::class => ConfigConstant::CONFIG_SHARED_TEMPLATE_PATH.'/articles/'.$action.'.html.twig',
            PageType::class => ConfigConstant::CONFIG_SHARED_TEMPLATE_PATH.'/pages/'.$action.'.html.twig',
            CategoryType::class => ConfigConstant::CONFIG_SHARED_TEMPLATE_PATH.'/categories/'.$action.'.html.twig',
            NavigationType::class => ConfigConstant::CONFIG_SHARED_TEMPLATE_PATH.'/navigations/'.$action.'.html.twig',
            LibraryType::class => ConfigConstant::CONFIG_INTERNAL_TEMPLATE_PATH.'/libraries/index.html.twig',
            default => null,
        };
        
        $formMethod = $type === 'index' ? '::provide()' : '::engine()';
        
        if (null === $template) {
            throw new InvalidArgumentException($this::class . $formMethod . ' expects a valid form type');
        }
        
        return $template;
    }

    private function setOptions(string $type): array
    {
        $options = [];
        $authors = match ($type) {
            ArticleType::class => $this->provideAuthors($this->authors['articles'], 'articles'),
            PageType::class => $this->provideAuthors($this->authors['pages'], 'pages'),
            default => null,
        };

        if (in_array($type, [ArticleType::class, PageType::class], true)) {
            $options['authors'] = $authors;
            $token = $this->security->getToken();
            $options['user_id'] = $token !== null ? $token->getUser() : null;
        }

        if (in_array($type, [ArticleType::class, PageType::class, NavigationType::class, CategoryType::class], true)) {
            $options['locale_fallback'] = $this->requestStack->getCurrentRequest()->getLocale();
        }

        return $options;
    }

    private function setData(mixed $data, string $type): object
    {
        if (null === $data) {
            $data = match ($type) {
                ArticleType::class => new BuilderArticle(),
                PageType::class => new BuilderPage(),
                CategoryType::class => new BuilderCategory(),
                NavigationType::class => new BuilderNavigation(),
                LibraryType::class => new BuilderLibrary(),
                default => null,
            };
        }

        if (null === $data) {
            throw new InvalidArgumentException($this::class . '::engine() expects a valid form type');
        }

        return $data;
    }

    private function saveData(FormInterface $form): void
    {
        $entity = $form->getData();
        $entity = match (true) {
            $entity instanceof BuilderNavigation =>
            $this->prepareNavigationData($form, $entity),
            $entity instanceof BuilderPage, $entity instanceof BuilderArticle, $entity instanceof BuilderCategory =>
            $this->prepareCmsEntitiesData($form, $entity),
            $form instanceof LibraryType => $this->assetManager->upload(
                $form->get('file')->getData(),
                $this->libraryLiipFilters
            ),
        };

        if (!$form instanceof LibraryType) {
            if ($entity->getId() !== null) {
                $this->entityManager->persist($entity);
            }
            $this->entityManager->flush();
            $this->callBackActions($entity);
        }
    }

    private function compileRedirectionRoute(?string $redirectionRoute, string $type): string
    {
        $request = $this->requestStack->getCurrentRequest();

        if ($request->request->get('vbe_save_and_reload') !== null) {
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
            throw new InvalidArgumentException($this::class . '::engine() expects a valid redirection route');
        }

        return $redirectionRoute ?
            $this->router->generate($redirectionRoute) : 
            $this->router->generate($routeToRedirect);
    }
    
    private function provideAuthors(array $authors, string $type): ?array
    {
        $class = $authors['author_class'];
        if ($class === null) {
            return null;
        }

        if (!class_exists($class)) {
            throw new InvalidArgumentException(sprintf(
                'Author provider class "%s" for %s does not exist.',
                $class,
                $type
            ));
        }
        
        $authors = $this->entityManager->getRepository($class)::findAll();
        $roles = $authors['author_roles'];

        if (is_array($roles) && !empty($roles) && method_exists($class, 'getRoles')) {
            $authors = array_filter($authors, function($author) use ($roles) {
                foreach ($author->getRoles() as $role) {
                    if (in_array($role, $roles, true)) {
                        return true;
                    }
                }
                return false;
            });
        }

        $placeholder = $authors['author_placeholder'];

        return array_map(function($author) use ($placeholder, $class, $type) {

            $label = [];
            foreach ($placeholder as $segment) {
                $segment = 'get' . ucfirst($segment);
                if (method_exists($author, $segment)) {
                    $label[] = $author->$segment();
                } else {
                    throw new InvalidArgumentException(sprintf(
                        'Author provider class "%s" for %s does not have a "%s()" method.',
                        $class,
                        $type,
                        $segment
                    ));
                }
            }

            return ['id' => $author->getId(), 'label' => implode(' ', $label)];
        }, $authors);
    }

    public function getAvailableLocales(): array
    {
        return $this->localeProvider->getList(false);
    }
    
    public function translateCrudTitle(string $entity, string $type): string
    {
        if ($entity === 'libraries' && $type === 'index') {
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
        $isEdition = $entity->getId() !== null;
        if ($entity instanceof BuilderPage) {
            $this->gedManager->setGedsFromPageBuilder($entity);
            if ($isEdition) {
                $this->cmsManager->createPageFile($entity);
            }
        }
    }

    private function prepareNavigationData(FormInterface $form, BuilderNavigation $entity): BuilderNavigation
    {
        $json = json_decode($form->getData()['data'], true);
        $json = is_array($json) ? $json : [];
        $entity->setContent($json);

        return $entity;
    }

    private function prepareCmsEntitiesData(
        FormInterface $form,
        BuilderPage|BuilderArticle|BuilderCategory $entity
    ): BuilderPage|BuilderArticle|BuilderCategory
    {
        $isEdition = $entity->getId() !== null;
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

            throw new InvalidEntityRepository(
                $this->translator->trans('error.'.$entity.'.not.found', [], 'BuilderEngineBundle-errors')
            );
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
            throw new InvalidArgumentException($this::class . '::remove() expects a valid redirection route');
        }

        $this->entityManager->remove($data);
        $this->entityManager->flush();
        
        return new RedirectResponse($this->router->generate($routeToRedirect));
    }
    
    protected function checkPermissions(): void
    {
        $currentRoute = $this->requestStack->getCurrentRequest()->attributes->get('_route');

        foreach ($this->customRoutes as $entity => $routes) {
            foreach ($routes as $action => $route) {

                if ($currentRoute === $route) {
                    $index = str_replace('_routes', '' ,$entity);
                    $this->isGranted($this->actions[$index][$action]['roles']);
                }
            }
        }
    }

    public function getFlagFromLocale(string $locale, array $flags): ?string
    {
        if (isset($flags[$locale])) {
            $data = $flags[$locale];
            return sprintf('<span title="%s">%s</span>', $data['language'], $data['flag']);
        }

        return null;
    }
}
