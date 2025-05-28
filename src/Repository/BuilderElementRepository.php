<?php

namespace VeeZions\BuilderEngine\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use VeeZions\BuilderEngine\Entity\BuilderElement;

/**
 * @extends ServiceEntityRepository<BuilderElement>
 */
class BuilderElementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BuilderElement::class);
    }
}
