<?php

namespace App\Repository;

use App\Entity\Tint;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Tint|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tint|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tint[]    findAll()
 * @method Tint[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TintRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tint::class);
    }

    // /**
    //  * @return Tint[] Returns an array of Tint objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Tint
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
