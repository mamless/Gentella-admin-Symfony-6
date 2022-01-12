<?php

namespace App\Repository;

use App\Entity\User;
use App\Repository\Interfaces\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class UserRepository
 * @package App\Repository
 */
final class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    /**
     * @var \Doctrine\Persistence\ObjectRepository
     */
    private $userRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager);
        $this->userRepository = $this->_em->getRepository(User::class);
    }

    public function findOneByUsernameOrEmail($query): ?User
    {
        return $this->userRepository->createQueryBuilder('u')
            ->andWhere('u.email = :val or u.username = :val')
            ->setParameter('val', $query)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function changeValidity(User $user)
    {
        if ($user->isValid())
            $user->setValid(false);
        else
            $user->setValid(true);
        $this->save($user);
        return $user;
    }

    /**
     * @param User $user
     * @return User|mixed|void
     */
    public function deleteSafe(User $user)
    {
        $user->setDeleted(true);
        $this->save($user);
        return $user;
    }

    public function search($data, $page = 0, $max = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $query = isset($data['query']) && $data['query'] ? $data['query'] : null;
        $order = isset($data['order']) && $data['order'] ? $data['order'] : null;
        $qb
            ->select('u')
            ->from(User::class, 'u');
        if ($query) {
            foreach ($query as $field) {
                if ($field['search']['value'] != '') {
                    if (in_array($field['data'], ['id', 'username', 'nomComplet', 'email'])) {
                        $qb
                            ->andWhere('u.' . $field['data'] . ' like :' . $field['data'])
                            ->setParameter($field['data'], "%" . $field['search']['value'] . "%");
                    }
                }
            }
        }
        $qb->groupBy('u.id');
        $qb->orderBy('u.id', 'DESC');
        if ($order) {
            $qb->orderBy('u.' . $query[$order[0]['column']]['data'], $order[0]['dir']);
        } else {
            $qb->orderBy('u.id', 'DESC');
        }
        return $this->paginate($qb, $page, $max);
    }

    /**
     * @param $id
     * @return object|null
     */
    public function find($id){
        return $this->userRepository->find($id);
    }

    /**
     * @param array $criteria
     * @return object|null
     */
    public function findOneBy(array $criteria){
        return $this->userRepository->findOneBy($criteria);
    }

    /**
     * @return object[]
     */
    public function findAll(){
        return $this->userRepository->findAll();
    }

    /**
     * @param array $criteria
     * @param array|null $orderBy
     * @param null $limit
     * @param null $offset
     * @return object[]
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null){
        return $this->userRepository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param array $criteria
     * @return mixed
     */
    public function count(array $criteria){
        return $this->userRepository->count($criteria);
    }
}
