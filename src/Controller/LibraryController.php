<?php

namespace VeeZions\BuilderEngine\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use VeeZions\BuilderEngine\Entity\BuilderLibrary;
use VeeZions\BuilderEngine\Form\LibraryType;
use VeeZions\BuilderEngine\Manager\EngineManager;

class LibraryController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private EngineManager $engineManager,
    ) {
    }

    public function index(): Response
    {
        return $this->engineManager->new(LibraryType::class, [
            'data' => $this->entityManager->getRepository(BuilderLibrary::class)->paginate()
        ]);
    }
}
