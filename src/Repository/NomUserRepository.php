<?php

namespace App\Repository;

use App\Entity\NomUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method NomUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method NomUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method NomUser[]    findAll()
 * @method NomUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NomUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NomUser::class);
    }

    // /**
    //  * @return NomUser[] Returns an array of NomUser objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?NomUser
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
