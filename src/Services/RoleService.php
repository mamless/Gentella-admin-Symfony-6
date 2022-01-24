<?php
namespace App\Services;

use App\Repository\UserRepository;

class RoleService extends BaseService
{
    protected $header = [
        'id' => 'ID',
        'roleName' => 'RoleName',
        'libelle' => 'Libelle',
        'permissions' => 'Permissions',
    ];

}