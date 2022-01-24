<?php
namespace App\Services;

use App\Repository\Interfaces\UserRepositoryInterface;
use App\Repository\UserRepository;

class UserService extends BaseService
{

    protected $header = [
        'id' => 'ID',
        'username' => 'Username',
        'email' => 'Email',
        'nomComplet' => 'Fullname',
        'status' => 'Status',
    ];
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->repository=$userRepository;
    }


}