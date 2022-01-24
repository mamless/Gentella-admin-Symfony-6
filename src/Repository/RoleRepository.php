<?php

namespace App\Repository;

use App\Entity\Permission;
use App\Entity\Role;
use App\Repository\Interfaces\RoleRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class roleRepository
 * @package App\Repository
 */
final class RoleRepository extends BaseRepository implements RoleRepositoryInterface
{
    /**
     * @var \Doctrine\Persistence\ObjectRepository
     */
    private $roleRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager);
        $this->roleRepository = $this->_em->getRepository(Role::class);
    }

    public function search($data, $page = 0, $max = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $query = isset($data['query']) && $data['query'] ? $data['query'] : null;
        $order = isset($data['order']) && $data['order'] ? $data['order'] : null;
        $qb
            ->select('R')
            ->from(Role::class, 'R');

        if ($query) {
            foreach ($query as $field) {
                if ($field['search']['value'] != '') {
                    if (in_array($field['data'], ['id', 'roleName', 'libelle'])) {
                        $qb
                            ->andWhere('R.' . $field['data'] . ' like :' . $field['data'])
                            ->setParameter($field['data'], "%" . $field['search']['value'] . "%");
                    }
                    if ($field['data'] == 'permissions') {
                        $qb->innerJoin('R.permissions','p')
                            ->andWhere('p.name like :' . $field['data'])
                            ->setParameter($field['data'], "%" . $field['search']['value'] . "%");
                    }
                }
            }
        }
        $qb->where('R.deleted=0');
        $qb->groupBy('R.id');
        $qb->orderBy('R.id', 'DESC');
//        if ($order) {
//            $qb->orderBy('R.' . $query[$order[0]['column']]['data'], $order[0]['dir']);
//        } else {
            $qb->orderBy('R.id', 'DESC');
//        }

        return $this->paginate($qb, $page, $max);
    }

    /**
     * @param $id
     * @return object|null
     */
    public function find($id)
    {
        return $this->roleRepository->find($id);
    }

    /**
     * @param array $criteria
     * @return object|null
     */
    public function findOneBy(array $criteria)
    {
        return $this->roleRepository->findOneBy($criteria);
    }

    /**
     * @return object[]
     */
    public function findAll()
    {
        return $this->roleRepository->findAll();
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
        return $this->roleRepository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param array $criteria
     * @return mixed
     */
    public function count(array $criteria)
    {
        return $this->roleRepository->count($criteria);
    }

    /**
     * @param Role $role
     * @param $data
     * @return Role|mixed
     */
    public function createOrUpdate(Role $role, $data){
        if(!empty($data['name'])){
            $role->setRoleName($data['name']);
        }
       if(!empty($data['libelle'])){
           $role->setLibelle($data['libelle']);
       }
        if(!empty($permissions=$data['permissions'])){
           foreach ($permissions as $permission){
               $role->addPermission($permission);
           }
        }
        $this->save($role);
        return $role;
    }


}
