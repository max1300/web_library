<?php

namespace App\Repository;

use App\Entity\TopicFramework;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TopicFramework|null find($id, $lockMode = null, $lockVersion = null)
 * @method TopicFramework|null findOneBy(array $criteria, array $orderBy = null)
 * @method TopicFramework[]    findAll()
 * @method TopicFramework[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TopicFrameworkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TopicFramework::class);
    }

    // /**
    //  * @return TopicFramework[] Returns an array of TopicFramework objects
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
    public function findOneBySomeField($value): ?TopicFramework
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
