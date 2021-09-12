<?php

namespace App\Repository;

use App\Entity\FavoritesPosts;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FavoritesPosts|null find($id, $lockMode = null, $lockVersion = null)
 * @method FavoritesPosts|null findOneBy(array $criteria, array $orderBy = null)
 * @method FavoritesPosts[]    findAll()
 * @method FavoritesPosts[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FavoritesPostsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FavoritesPosts::class);
    }

    // /**
    //  * @return FavoritesPosts[] Returns an array of FavoritesPosts objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FavoritesPosts
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
