<?php

namespace App\Repository;

use App\Entity\NgCity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method NgCity|null find($id, $lockMode = null, $lockVersion = null)
 * @method NgCity|null findOneBy(array $criteria, array $orderBy = null)
 * @method NgCity[]    findAll()
 * @method NgCity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NgCityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NgCity::class);
    }

    // /**
    //  * @return NgCity[] Returns an array of NgCity objects
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
    public function findOneBySomeField($value): ?NgCity
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
