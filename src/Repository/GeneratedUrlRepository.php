<?php

namespace App\Repository;

use App\Entity\GeneratedUrl;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GeneratedUrl|null find($id, $lockMode = null, $lockVersion = null)
 * @method GeneratedUrl|null findOneBy(array $criteria, array $orderBy = null)
 * @method GeneratedUrl[]    findAll()
 * @method GeneratedUrl[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GeneratedUrlRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GeneratedUrl::class);
    }

    // /**
    //  * @return GeneratedUrl[] Returns an array of GeneratedUrl objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?GeneratedUrl
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
