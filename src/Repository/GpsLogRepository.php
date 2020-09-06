<?php

namespace App\Repository;

use App\Entity\GpsLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GpsLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method GpsLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method GpsLog[]    findAll()
 * @method GpsLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GpsLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GpsLog::class);
    }

    // /**
    //  * @return GpsLog[] Returns an array of GpsLog objects
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
    public function findOneBySomeField($value): ?GpsLog
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
