<?php

namespace App\Repository;

use App\Entity\TopicProgrammingLanguage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TopicProgrammingLanguage|null find($id, $lockMode = null, $lockVersion = null)
 * @method TopicProgrammingLanguage|null findOneBy(array $criteria, array $orderBy = null)
 * @method TopicProgrammingLanguage[]    findAll()
 * @method TopicProgrammingLanguage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TopicProgrammingLanguageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TopicProgrammingLanguage::class);
    }

    // /**
    //  * @return TopicProgrammingLanguage[] Returns an array of TopicProgrammingLanguage objects
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
    public function findOneBySomeField($value): ?TopicProgrammingLanguage
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
