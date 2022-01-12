<?php
namespace App\Services;

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

}