<?php

/**
 * @author BaBeuloula <info@babeuloula.fr>
 */

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Configuration;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Configuration|null find($id, $lockMode = null, $lockVersion = null)
 * @method Configuration|null findOneBy(array $criteria, array $orderBy = null)
 * @method Configuration[]    findAll()
 * @method Configuration[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConfigurationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Configuration::class);
    }
}
