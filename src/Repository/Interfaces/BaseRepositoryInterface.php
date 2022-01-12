<?php

namespace App\Repository\Interfaces;


use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

interface BaseRepositoryInterface
{
    /**
     * @param $entity
     * @return mixed
     */
    public function save($entity);
    /**
     * @param $entity
     * @return mixed
     */
    public function delete($entity);
    /**
     * @param QueryBuilder $queryBuilder
     * @param $page
     * @param $max
     * @return Paginator
     */
    public function paginate(QueryBuilder $queryBuilder, $page, $max);

}
