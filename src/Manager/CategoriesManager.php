<?php

namespace VeeZions\BuilderEngine\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\RequestStack;
use VeeZions\BuilderEngine\Entity\BuilderCategory;

class CategoriesManager
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private RequestStack $requestStack,
    ) {
    }

    /**
     * @return array<int, array<string, mixed>|BuilderCategory>
     */
    public function getCategoriesTree(bool $formTypeTree = false): array
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            throw new InvalidArgumentException($this::class.'::getCategoriesTree() expects a valid request');
        }

        $locale = $request->getLocale();
        $categories = $this->entityManager->getRepository(BuilderCategory::class)->findBy(['locale' => $locale]);

        return $formTypeTree ? $this->buildHierarchy($categories) : $this->buildTree($categories);
    }

    /**
     * @param array<int, BuilderCategory> $categories
     *
     * @return array<int, array<string, mixed>>
     */
    public function buildTree(array $categories): array
    {
        $indexed = [];
        foreach ($categories as $category) {
            $indexed[$category->getId()] = [
                'entity' => $category,
                'children' => [],
            ];
        }

        $tree = [];
        foreach ($indexed as $id => $node) {
            $parent = $node['entity']->getParent();
            if (null !== $parent) {
                $indexed[$parent->getId()]['children'][] = &$indexed[$id];
            } else {
                $tree[] = &$indexed[$id];
            }
        }

        $map = array_map([$this, 'normalizeNode'], $tree);

        return $map;
    }

    /**
     * @param array<string, mixed> $node
     *
     * @return array<string, mixed>
     */
    private function normalizeNode(array $node): array
    {
        $cat = $node['entity'];
        if (!$cat instanceof BuilderCategory) {
            throw new InvalidArgumentException('$node[entity] must implement BuilderCategory');
        }

        $children = $node['children'];
        if (!is_array($children)) {
            throw new InvalidArgumentException('$node[children] must be an array');
        }

        return [
            'id' => $cat->getId(),
            'name' => $cat->getTitle(),
            'children' => array_map([$this, 'normalizeNode'], $children),
        ];
    }

    /**
     * @param array<int, BuilderCategory> $categories
     * 
     * @return array<int<0, max>, BuilderCategory>
     */
    public function buildHierarchy(array $categories): array
    {
        $parents = [];
        $children = [];

        foreach ($categories as $category) {
            if ($category->getParent() === null) {
                $parents[] = $category;
            } else {
                $children[$category->getParent()->getId()][] = $category;
            }
        }

        usort($parents, fn($a, $b) => strcmp($a->getTitle() ?? '', $b->getTitle() ?? ''));

        $result = [];
        foreach ($parents as $parent) {
            $result[] = $parent;
            if (isset($children[$parent->getId()])) {
                usort($children[$parent->getId()], fn($a, $b) => strcmp($a->getTitle() ?? '', $b->getTitle() ?? ''));
                foreach ($children[$parent->getId()] as $child) {
                    /**@phpstan-ignore-next-line*/
                    $child->level = 1;
                    $result[] = $child;
                }
            }
        }

        return $result;
    }
}
