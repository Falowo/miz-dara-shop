<?php

namespace App\Repository;

use App\Entity\DeliveryFees;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DeliveryFees|null find($id, $lockMode = null, $lockVersion = null)
 * @method DeliveryFees|null findOneBy(array $criteria, array $orderBy = null)
 * @method DeliveryFees[]    findAll()
 * @method DeliveryFees[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeliveryFeesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DeliveryFees::class);
    }

    // /**
    //  * @return DeliveryFees[] Returns an array of DeliveryFees objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DeliveryFees
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
