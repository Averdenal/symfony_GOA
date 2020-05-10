<?php

namespace App\Repository;

use App\Entity\AffGroupe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AffGroupe|null find($id, $lockMode = null, $lockVersion = null)
 * @method AffGroupe|null findOneBy(array $criteria, array $orderBy = null)
 * @method AffGroupe[]    findAll()
 * @method AffGroupe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AffGroupeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AffGroupe::class);
    }

    // /**
    //  * @return AffGroupe[] Returns an array of AffGroupe objects
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
    public function findOneBySomeField($value): ?AffGroupe
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
