<?php

namespace XenoLab\XenoEngine\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use XenoLab\XenoEngine\Entity\XenoElement;

/**
 * @extends ServiceEntityRepository<XenoElement>
 */
class ElementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, XenoElement::class);
    }
}
