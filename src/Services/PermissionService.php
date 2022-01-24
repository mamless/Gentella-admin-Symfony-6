<?php
namespace App\Services;

use App\Repository\Interfaces\PermissionRepositoryInterface;

class PermissionService extends BaseService
{
    protected $header = [
        'id' => 'ID',
        'name' => 'Nom de la permission',
    ];
    public function __construct(PermissionRepositoryInterface $permissionRepository)
    {
        $this->repository=$permissionRepository;
    }

}