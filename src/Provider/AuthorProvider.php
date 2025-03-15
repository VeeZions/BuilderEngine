<?php

namespace VeeZions\BuilderEngine\Provider;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Exception\InvalidArgumentException;

class AuthorProvider
{
    public function __construct(
        private EntityManagerInterface $entityManager
    )
    {
        
    }
    
    public function provideAuthors(array $authors, string $type): ?array
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

        $authorsProvider = $this->entityManager->getRepository($class)->findAll();
        $role = $authors['author_role'];

        if (null !== $role && strlen($role) > 0 && method_exists($class, 'getRoles')) {
            $authorsProvider = array_filter($authorsProvider, function($author) use ($role) {
                $roles = $author->getRoles();
                if (in_array($role, $roles, true)) {
                    return true;
                }
                return false;
            });
        }

        $placeholder = $authors['author_placeholder'];

        return array_map(function($author) use ($placeholder, $class, $type) {

            $placeholder = 'get' . ucfirst($placeholder);
            if (!method_exists($author, $placeholder)) {
                throw new InvalidArgumentException(sprintf(
                    'Author provider class "%s" for %s does not have a "%s()" method.',
                    $class,
                    $type,
                    $placeholder
                ));
            }

            return ['id' => $author->getId(), 'label' => $author->$placeholder()];
        }, $authorsProvider);
    }
}
