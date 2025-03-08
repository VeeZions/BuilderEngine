<?php

namespace Vision\BuilderEngine\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Vision\BuilderEngine\Entity\BuilderNavigation;
use Vision\BuilderEngine\Entity\Page;

readonly class CmsManager
{
    public function __construct(
        private ParameterBagInterface $parameterBag,
        private Filesystem $filesystem,
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @return array<int, string>
     */
    public function getCmsModulesAvailable(): array
    {
        $projectDir = $this->parameterBag->get('kernel.project_dir');
        if (!is_string($projectDir)) {
            throw new \RuntimeException('Project directory must be a string');
        }

        $modulesTwig = $projectDir.'/templates/macros/cms-modules/';
        $finder = new Finder();
        $finder->files()->in($modulesTwig);
        $modules = [];

        foreach ($finder as $file) {
            $modules[] = str_replace('.html.twig', '', $file->getBasename());
        }

        return $modules;
    }

    public function createPageFile(Page $page): void
    {
        $projectDir = $this->parameterBag->get('kernel.project_dir');
        if (!is_string($projectDir)) {
            throw new \RuntimeException('Project directory must be a string');
        }

        $route = $page->getRoute() ?? '';
        $clean = str_replace('app_web_', '', $route);
        $slug = str_replace('_', '/', $clean);
        $controller = '';
        foreach (explode('_', $clean) as $segment) {
            $controller .= ucfirst($segment);
        }

        $stub = $projectDir.'/resources/stubs/new-page.stub';
        $destination = $projectDir.'/src/Controller/Web/'.$controller.'Controller.php';

        if ($this->filesystem->exists($stub)) {
            $content = file_get_contents($stub);
            $content = str_replace(['{{ controller }}', '{{ route }}', '{{ slug }}'], [$controller, $route, $slug], is_string($content) ? $content : '');
            $this->filesystem->dumpFile($destination, $content);
        }
    }

    public function removePageFile(Page $page): void
    {
        $projectDir = $this->parameterBag->get('kernel.project_dir');
        if (!is_string($projectDir)) {
            throw new \RuntimeException('Project directory must be a string');
        }

        $route = $page->getRoute() ?? '';
        $clean = str_replace('app_web_', '', $route);
        $controller = '';
        foreach (explode('_', $clean) as $segment) {
            $controller .= ucfirst($segment);
        }
        $file = $projectDir.'/src/Controller/Web/'.$controller.'Controller.php';

        if ($this->filesystem->exists($file)) {
            $this->filesystem->remove($file);
        }
    }

    public function removeFromNavigation(Page $page): void
    {
        $navigations = $this->entityManager->getRepository(BuilderNavigation::class)->findAll();
        $filtered = ['name' => null, 'stages' => []];
        foreach ($navigations as $navigation) {
            $content = $navigation->getContent();
            if (null !== $content) {
                $target = $page->getRoute() ?? '';
                $filtered['name'] = $content['name'];
                $stages = (array) $content['stages'];
                $s = [];
                foreach ($stages as $stage) {
                    $r = [];
                    foreach ((array) $stage as $link) {
                        if (is_array($link) && $link['route'] !== $target) {
                            $l = [];
                            if (isset($link['children'])) {
                                foreach ((array) $link['children'] as $child) {
                                    if ($child['route'] !== $target) {
                                        $l[] = $child;
                                    }
                                }
                            }
                            $link['children'] = $l;
                            $r[] = $link;
                        }
                    }
                    if (!empty($r)) {
                        $s[] = $r;
                    }
                }
                $filtered['stages'] = $s;
                $navigation->setContent($filtered);
                $this->entityManager->flush();
            }
        }
    }
}
