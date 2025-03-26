<?php

namespace VeeZions\BuilderEngine\Twig\Runtime;

use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormView;
use Twig\Extension\RuntimeExtensionInterface;
use Twig\Markup;
use VeeZions\BuilderEngine\Constant\ConfigConstant;
use VeeZions\BuilderEngine\Entity\BuilderArticle;
use VeeZions\BuilderEngine\Entity\BuilderCategory;
use VeeZions\BuilderEngine\Entity\BuilderElement;
use VeeZions\BuilderEngine\Entity\BuilderLibrary;
use VeeZions\BuilderEngine\Entity\BuilderNavigation;
use VeeZions\BuilderEngine\Entity\BuilderPage;
use VeeZions\BuilderEngine\Manager\AssetManager;
use VeeZions\BuilderEngine\Manager\FormManager;
use VeeZions\BuilderEngine\Manager\GedManager;
use VeeZions\BuilderEngine\Manager\HtmlManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Carbon\Carbon;
use Symfony\Component\Intl\Locales;

final class FiltersRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private HtmlManager $htmlManager,
        private RequestStack $requestStack,
        private FormManager $formManager,
        private array $customRoutes,
        private bool $page_title,
        private AssetManager $manageAsset,
        private EntityManagerInterface $entityManager,
        private GedManager $gedManager,
    )
    {
        
    }

    public function containerId(): ?string
    {
        $route = $this->requestStack->getCurrentRequest()->attributes->get('_route');
        if (!str_starts_with($route, 'xlxeb_controller_')) {
            return null;
        }

        return sprintf(
            'vbe-%s',
            str_replace(
                ['xlxeb_controller_', '_', '.', 'index'],
                ['', '-', '-', 'crud'],
                $route
            )
        );
    }

    public function buildRender(PaginationInterface $data, array $order = []): Markup
    {
        return new Markup($this->htmlManager->buildRender($data, $order), 'UTF-8');
    }

    public function buildTable(PaginationInterface $data): Markup
    {
        return new Markup($this->htmlManager->buildTable($data), 'UTF-8');
    }

    public function buildFilters(PaginationInterface $data): Markup
    {
        return new Markup($this->htmlManager->buildFilters($data), 'UTF-8');
    }

    public function buildPagination(PaginationInterface $data): Markup
    {
        return new Markup($this->htmlManager->buildPagination($data), 'UTF-8');
    }

    public function buildCounter(PaginationInterface $data): Markup
    {
        return new Markup($this->htmlManager->buildCounter($data), 'UTF-8');
    }

    public function buildCreate(PaginationInterface $data): Markup
    {
        return new Markup($this->htmlManager->buildCreate($data), 'UTF-8');
    }

    public function valueMutator(array $cell, array $flags): ?Markup
    {
        $value = $cell['value'];

        if ($cell['label'] === 'locale') {
            $flag = $this->formManager->getFlagFromLocale($value, $flags);
            return new Markup($flag, 'UTF-8');
        }

        if ($value instanceof DateTimeInterface) {
            Carbon::setLocale($this->requestStack->getCurrentRequest()->getLocale());
            return new Markup(Carbon::parse($value)->isoFormat('lll'), 'UTF-8');
        }

        if (is_bool($value)) {
            return new Markup($value
                ? '<svg width="16" class="vbe-boolean-table-icons vbe-booleans-true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M256 48a208 208 0 1 1 0 416 208 208 0 1 1 0-416zm0 464A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209c9.4-9.4 9.4-24.6 0-33.9s-24.6-9.4-33.9 0l-111 111-47-47c-9.4-9.4-24.6-9.4-33.9 0s-9.4 24.6 0 33.9l64 64c9.4 9.4 24.6 9.4 33.9 0L369 209z"/></svg>'
                : '<svg width="16" class="vbe-boolean-table-icons vbe-booleans-false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M256 48a208 208 0 1 1 0 416 208 208 0 1 1 0-416zm0 464A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM175 175c-9.4 9.4-9.4 24.6 0 33.9l47 47-47 47c-9.4 9.4-9.4 24.6 0 33.9s24.6 9.4 33.9 0l47-47 47 47c9.4 9.4 24.6 9.4 33.9 0s9.4-24.6 0-33.9l-47-47 47-47c9.4-9.4 9.4-24.6 0-33.9s-24.6-9.4-33.9 0l-47 47-47-47c-9.4-9.4-24.6-9.4-33.9 0z"/></svg>', 'UTF-8');
        }

        if (is_scalar($value)) {
            return new Markup((string) $value, 'UTF-8');
        }

        return null;
    }

    public function isSortable(string $column): bool
    {
        return !str_contains($column, ' as ');
    }

    public function getPageTitle(): ?string
    {
        if (!$this->page_title) {
            return null;
        }
        
        $route = $this->requestStack->getCurrentRequest()->attributes->get('_route');
        if ($route === ConfigConstant::CONFIG_DEFAULT_ROUTES['libraries_routes']['list']) {
            return $this->formManager->translateCrudTitle('libraries', 'index');
        }
        $entity = null;
        $controller = null;
        foreach ($this->customRoutes as $type => $list) {
            foreach ($list as $action => $customRoute) {
                if ($route === $customRoute) {
                    $controller = $action === 'list' ? 'index' : $action;
                    $entity = str_replace('_routes', '', $type);

                    if ($controller !== 'index') {
                        $entity = match ($entity) {
                            'articles' => 'article',
                            'pages' => 'page',
                            'categories' => 'category',
                            'navigations' => 'navigation',
                            'libraries' => 'library',
                            default => null,
                        };
                    }
                }
            }
        }

        if (null === $entity || null === $controller) {
            return null;
        }

        return $this->formManager->translateCrudTitle($entity, $controller);
    }

    public function getFlags(): array
    {
        return $this->formManager->getAvailableLocales();
    }

    public function removeAlias(?string $colName): ?string
    {
        if (null === $colName) {
            return null;
        }

        $split = explode(' as ', $colName);
        return $split[0];
    }

    public function awsS3(?string $value, bool $fromCdn = true): ?string
    {
        return false === $fromCdn
            ? $this->manageAsset->getUrlFromAwsS3($value)
            : $this->manageAsset->getUrlFromCdn($value);
    }

    /**
     * @return array<string, string>
     */
    public function fileExtension(string $url): array
    {
        $split = explode('.', $url);
        $ext = end($split);
        $color = match ($ext) {
            'jpg', 'jpeg', 'png', 'gif' => '#bb99ff',
            'avi', 'mpeg', 'mov', 'mp4', 'wmv' => '#e68a00',
            'pdf' => '#cc3300',
            'xls', 'xlsx', 'csv' => '#009933',
            'doc', 'docx' => '#3366cc',
            default => '#000',
        };

        return [
            'ext' => strtoupper(end($split)),
            'color' => $color,
        ];
    }

    public function getMediaName(string $url): string
    {
        $split = explode(DIRECTORY_SEPARATOR, $url);
        $name = end($split);
        $split2 = explode('.', $name);
        $ext = end($split2);

        return str_replace('.'.$ext, '', $name);
    }

    public function humanizeSize(int $octets): string
    {
        $def = [
            [1, 'octets'],
            [1024, 'ko'],
            [1024 * 1024, 'Mo'],
            [1024 * 1024 * 1024, 'Go'],
            [1024 * 1024 * 1024 * 1024, 'To'],
        ];

        for ($i = 0; $i < count($def); ++$i) {
            if ($octets < $def[$i][0]) {
                return number_format($octets / $def[$i - 1][0], 2, ',', ' ').' '.$def[$i - 1][1];
            }
        }

        return '';
    }

    public function getAllElementsFromPage(BuilderPage $page): array
    {
        $elements = $this->entityManager->getRepository(BuilderElement::class)->findBy(['page' => $page]);
        $results = [];
        foreach ($elements as $element) {
            $results[] = $this->gedManager->getElementHumanizedRanking($element);
        }

        return $results;
    }

    public function getRealElementsLength(array $elements): int
    {
        $count = 0;
        foreach ($elements as $element) {
            $count += $this->entityManager->getRepository(BuilderElement::class)->count(['page' => $element->getPage()]);
        }

        return $count;
    }
}
