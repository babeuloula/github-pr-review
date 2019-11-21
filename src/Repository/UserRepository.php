<?php

/**
 * @author BaBeuloula <info@babeuloula.fr>
 */

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(User $user): User
    {
        if (false === $this->getEntityManager()->contains($user)) {
            $this->getEntityManager()->persist($user);
        }

        $this->getEntityManager()->flush();

        return $user;
    }

    public function findByNickname(?string $nickname): ?User
    {
        if (false === \is_string($nickname)) {
            return null;
        }

        return $this
            ->createQueryBuilder('user')
            ->where('user.nickname = :nickname')
            ->setParameter('nickname', $nickname)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function delete(User $user): void
    {
        $this
            ->getEntityManager()
            ->remove($user)
        ;

        $this
            ->getEntityManager()
            ->flush()
        ;
    }
}
