<?php

namespace App\Repository\Interfaces;


use App\Entity\User;

interface UserRepositoryInterface extends BaseRepositoryInterface
{

    /**
     * @param User $user
     * @return mixed
     */
    public function changeValidity(User $user);

    /**
     * @param $data
     * @param int $page
     * @param null $max
     * @return mixed
     */
    public function search($data, $page = 0, $max = null);

    /**
     * @param $id
     * @return mixed
     */
    public function find($id);

    /**
     * @param array $criteria
     * @return mixed
     */
    public function findOneBy(array $criteria);

    /**
     * @param array $criteria
     * @param array|null $orderBy
     * @param null $limit
     * @param null $offset
     * @return mixed
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param array $criteria
     * @return mixed
     */
    public function count(array $criteria);

    /**
     * @param User $user
     * @param $role
     * @param $encodedPassword
     * @return mixed
     */
    public function createOrUpdate(User $user, $role,  $encodedPassword);

    /**
     * @param $role
     * @return mixed
     */
    public function getUserByRole($role);
}
