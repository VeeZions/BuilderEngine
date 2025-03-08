<?php

namespace Vision\BuilderEngine\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Intl\Locales;
use Symfony\Component\Routing\Router;
use Symfony\Contracts\Translation\TranslatorInterface;
use Vision\BuilderEngine\Constant\AssetConstant;
use Vision\BuilderEngine\Constant\ConfigConstant;
use Vision\BuilderEngine\Entity\BuilderArticle;
use Vision\BuilderEngine\Entity\BuilderPage;
use Vision\BuilderEngine\Entity\BuilderCategory;
use Vision\BuilderEngine\Entity\BuilderLibrary;
use Vision\BuilderEngine\Entity\BuilderNavigation;
use Vision\BuilderEngine\Form\ArticleType;
use Vision\BuilderEngine\Form\CategoryType;
use Vision\BuilderEngine\Form\LibraryType;
use Vision\BuilderEngine\Form\NavigationType;
use Vision\BuilderEngine\Form\PageType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface as Security;

class FormManager
{
    public function __construct(
        private FormFactory $formFactory,
        private RequestStack $requestStack,
        private EntityManagerInterface $entityManager,
        private Security $security,
        private TranslatorInterface $translator,
        private Router $router,
        private GedManager $gedManager,
        private CmsManager $cmsManager,
        private AssetManager $assetManager,
        private array $authors,
        private string $bundleMode,
        private array $libraryLiipFilters
    )
    {
        
    }
    
    public function form(string $type, mixed $data = null, ?string $redictionRoute = null): FormView
    {
        if ($this->bundleMode === ConfigConstant::CONFIG_MODE_FORM) {
            throw new InvalidArgumentException($this::class . '::form() #Argument3 "$redictionRoute" must be set in "'.ConfigConstant::CONFIG_MODE_FORM.'" mode.');
        }
        
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
            throw new InvalidArgumentException($this::class . '::form() expects a valid form type');
        }
        
        $form = $this->formFactory->create($type, $data, $options);
        $form->handleRequest($this->requestStack->getCurrentRequest());

        dump($this->bundleMode);
        
        if ($form->isSubmitted() && $form->isValid()) {
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

            dd($entity);

            if (!$form instanceof LibraryType) {
                if ($entity->getId() !== null) {
                    $this->entityManager->persist($entity);
                }
                $this->entityManager->flush();
                $this->callBackActions($entity);
            }

            $routeToRedirect = match ($type) {
                ArticleType::class => 'xlxeb_controller_article_index',
                PageType::class => 'xlxeb_controller_page_index',
                CategoryType::class => 'xlxeb_controller_category_index',
                NavigationType::class => 'xlxeb_controller_navigation_index',
                LibraryType::class => 'xlxeb_controller_library_index',
                default => null,
            };

            if (null === $routeToRedirect) {
                throw new InvalidArgumentException($this::class . '::form() expects a valid redirection route');
            }

            $routeToRedirect = $redictionRoute ?? $routeToRedirect;

            return new RedirectResponse($this->router->generate($routeToRedirect));
        }

        return $form->createView();
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
        $flagsFolder = __DIR__.'/../../assets/media/flags';
        $filesystem = new Filesystem();

        if ($filesystem->exists($flagsFolder)) {
            $finder = new Finder();
            $files = $finder->files()->in($flagsFolder);
            $locales = [];

            foreach ($files as $file) {
                $locale = $file->getBasename('.png');

                if (strlen($locale) === 2
                    && isset(ConfigConstant::CONFIG_LOCALES[strtolower($locale)])
                    && Countries::exists($locale))
                {
                    $locales[$locale] = [
                        'alpha2' => $locale,
                        'name' => Countries::getName($locale),
                        'flag' => base64_encode($flagsFolder.'/'.$file->getBasename()),
                        'locale' => ConfigConstant::CONFIG_LOCALES[strtolower($locale)],
                    ];
                }
            }
        }
        ksort($locales);

        return $locales;
    }
    
    public function translateCrudTitle(string $entity, string $type): string
    {
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

        $newLibrary = $this->entityManager->getRepository(BuilderLibrary::class)->find($newLibraryId);
        if (null !== $newLibrary) {
            $entity->addLibrary($newLibrary);
        }

        return $entity;
    }
}
