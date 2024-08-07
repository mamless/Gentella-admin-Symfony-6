<?php

namespace App\Repository;

use App\Entity\Categorie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Categorie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Categorie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Categorie[]    findAll()
 * @method Categorie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategorieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Categorie::class);
    }

    // /**
    //  * @return Categorie[] Returns an array of Categorie objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Categorie
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function changeValidite(Categorie $categorie)
    {
        if ($categorie->getValid()) {
            $categorie->setValid(false);
        } else {
            $categorie->setValid(true);
        }
        $this->entityManager->persist($categorie);
        $this->entityManager->flush();

        return $categorie;
    }

    public function delete(Categorie $categorie)
    {
        $categorie->setDeleted(true)->oldify();
        $this->entityManager->persist($categorie);
        $this->entityManager->flush();

        return $categorie;
    }


    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function countChildCategories($id) : ?int
    {
        return (int) $this->createQueryBuilder('c')
            ->select('count(c.id)')
            ->andWhere("c.CategorieParente= :id")
            ->andWhere('c.deleted = false')
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleScalarResult()
            ;
    }


}
