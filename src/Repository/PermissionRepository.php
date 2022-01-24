<?php

namespace App\Repository;

use App\Entity\Permission;
use App\Entity\Role;
use App\Repository\Interfaces\PermissionRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class permissionRepository
 * @package App\Repository
 */
final class PermissionRepository extends BaseRepository implements PermissionRepositoryInterface
{
    /**
     * @var \Doctrine\Persistence\ObjectRepository
     */
    private $permissionRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager);
        $this->permissionRepository = $this->_em->getRepository(Permission::class);
    }

    public function search($data, $page = 0, $max = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $query = isset($data['query']) && $data['query'] ? $data['query'] : null;
        $order = isset($data['order']) && $data['order'] ? $data['order'] : null;
        $qb
            ->select('p')
            ->from(Permission::class, 'p');
        if ($query) {
            foreach ($query as $field) {
                if ($field['search']['value'] != '') {
                    if (in_array($field['data'], ['id', 'name'])) {
                        $qb
                            ->andWhere('p.' . $field['data'] . ' like :' . $field['data'])
                            ->setParameter($field['data'], "%" . $field['search']['value'] . "%");
                    }
                }
            }
        }
        $qb->groupBy('p.id');
        $qb->orderBy('p.id', 'DESC');
        if ($order) {
            $qb->orderBy('p.' . $query[$order[0]['column']]['data'], $order[0]['dir']);
        } else {
        $qb->orderBy('p.id', 'DESC');
        }
        return $this->paginate($qb, $page, $max);
    }

    /**
     * @param $id
     * @return object|null
     */
    public function find($id)
    {
        return $this->permissionRepository->find($id);
    }

    /**
     * @param array $criteria
     * @return object|null
     */
    public function findOneBy(array $criteria)
    {
        return $this->permissionRepository->findOneBy($criteria);
    }

    /**
     * @return object[]
     */
    public function findAll()
    {
        return $this->permissionRepository->findAll();
    }

    /**
     * @param array $criteria
     * @param array|null $orderBy
     * @param null $limit
     * @param null $offset
     * @return object[]
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->permissionRepository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param array $criteria
     * @return mixed
     */
    public function count(array $criteria)
    {
        return $this->permissionRepository->count($criteria);
    }

    /**
     * @param Permission $permission
     * @param $data
     * @return Role|mixed
     */
    public function createOrUpdate(Permission $permission, $data=[]){
        if(!empty($data['name'])){
            $permission->setName($data['name']);
        }
        $this->save($permission);
        return $permission;
    }

    /**
     * @param $ids_permission
     * @return mixed|object[]
     */
    public function getCollection($ids_permission){
        return $this->permissionRepository->findBy(array('id' => $ids_permission));
    }

}
