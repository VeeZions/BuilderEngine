<?php

namespace VeeZions\BuilderEngine\Provider;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Exception\InvalidArgumentException;

class AuthorProvider
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @param array<string, mixed> $authors
     *
     * @return array<int, array<string, mixed>>|null
     */
    public function provideAuthors(array $authors, string $type): ?array
    {
        $class = $authors['author_class'];
        if (!is_string($class)) {
            return null;
        }

        if (!class_exists($class)) {
            throw new InvalidArgumentException(sprintf('Author provider class "%s" for %s does not exist.', $class, $type));
        }

        $authorsProvider = $this->entityManager->getRepository($class)->findAll();
        $role = $authors['author_role'];

        if (is_string($role) && strlen($role) > 0 && method_exists($class, 'getRoles')) {
            $authorsProvider = array_filter($authorsProvider, function ($author) use ($role) {
                /**@phpstan-ignore-next-line */
                $roles = $author->getRoles();
                if (in_array($role, $roles, true)) {
                    return true;
                }

                return false;
            });
        }

        $placeholder = $authors['author_placeholder'];
        if (!is_string($placeholder) || 0 === strlen($placeholder)) {
            throw new InvalidArgumentException(sprintf('Author provider class "%s" for %s does not have a placeholder.', $class, $type));
        }

        return array_map(function ($author) use ($placeholder, $class, $type) {
            $placeholder = 'get'.ucfirst($placeholder);
            if (!method_exists($author, $placeholder)) {
                throw new InvalidArgumentException(sprintf('Author provider class "%s" for %s does not have a "%s()" method.', $class, $type, $placeholder));
            }

            /**@phpstan-ignore-next-line */
            return ['id' => $author->getId(), 'label' => $author->$placeholder()];
        }, $authorsProvider);
    }
}
