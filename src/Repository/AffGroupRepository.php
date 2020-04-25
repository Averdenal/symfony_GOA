<?php

namespace App\Repository;

use App\Entity\AffGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AffGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method AffGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method AffGroup[]    findAll()
 * @method AffGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AffGroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AffGroup::class);
    }

    // /**
    //  * @return AffGroup[] Returns an array of AffGroup objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AffGroup
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
