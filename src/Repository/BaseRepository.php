<?php

namespace App\Repository;


use App\Repository\Interfaces\BaseRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

abstract class BaseRepository implements BaseRepositoryInterface
{

    /**
     * @var EntityManagerInterface
     */
    protected $_em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->_em = $entityManager;
    }
    /**
     * @param object $entity
     */
    public function save($entity)
    {
        $this->_em->persist($entity);
        $this->_em->flush();
    }
    /**
     * @param object $entity
     */
    public function delete($entity)
    {
        $this->_em->remove($entity);
        $this->_em->flush();
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param $page
     * @param $max
     * @return Paginator
     */
    public function paginate(QueryBuilder $queryBuilder, $page, $max) {
        if ($max) {
            $preparedQuery = $queryBuilder->getQuery()
                ->setMaxResults($max)
                ->setFirstResult($page * $max);
        } else {
            $preparedQuery = $queryBuilder->getQuery();
        }
        return new Paginator($preparedQuery);
    }

}