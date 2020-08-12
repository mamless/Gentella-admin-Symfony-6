<?php

namespace App\Repository;

use App\Entity\OldPost;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OldPost|null find($id, $lockMode = null, $lockVersion = null)
 * @method OldPost|null findOneBy(array $criteria, array $orderBy = null)
 * @method OldPost[]    findAll()
 * @method OldPost[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OldPostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OldPost::class);
    }

    // /**
    //  * @return OldPost[] Returns an array of OldPost objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?OldPost
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
