<?php

namespace App\Repository;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{


    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @param $id
     * @return Query
     */
    public function findALLByCategoryQuery($id): Query
    {



        return $this->createQueryBuilder('p')
            ->select('p, i, c' )
             ->Join('p.categories', 'c')
            ->leftJoin('p.stocks', 's')
            ->leftJoin('p.images', 'i')
            // ->andWhere('i.isMainImage = true')
            ->andWhere('p.hasStock = true')
             ->andWhere('c.id = :id')
             ->setParameter('id', $id)
            ->orderBy('p.id', 'DESC')
            ->getQuery();
    }

    public function findAllByRandomQuery(?int $limit): Query
    {
        $queryBuilder = $this->findAllByRandomQueryBuilder();

        if (!is_null($limit)) {
            $queryBuilder = $queryBuilder
                ->setMaxResults($limit);
        }

        return $queryBuilder->getQuery();
    }


    public function findAllByRandomQueryBuilder(): QueryBuilder
    {
        $order = array_rand(array(
            //            'DESC' => 'DESC',
            'ASC' => 'ASC'
        ));

        $column = array_rand(array(
            'p.id' => 'p.id',
            //            'p.name' => 'p.name',
            //            'p.price' => 'p.price'
        ));
        return $this->createQueryBuilder('p')
        ->select('p, c')
            ->join('p.categories', 'c')
            ->orderBy($column, $order);
    }

    /*
    public function findOneBySomeField($value): ?Product
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
