<?php
namespace App\Services;

use App\Repository\Interfaces\RoleRepositoryInterface;

class RoleService extends BaseService
{
    protected $header = [
        'id' => 'ID',
        'roleName' => 'RoleName',
        'libelle' => 'Libelle',
        'permissions' => 'Permissions',
    ];
    public function __construct(RoleRepositoryInterface $roleRepository)
    {
        $this->repository=$roleRepository;
    }

}