<?php

namespace App\Repositories;

use App\Models\Role;
use App\Repositories\Interfaces\RoleRepositoryInterface;

class RoleRepository extends BaseRepository implements RoleRepositoryInterface
{
    public function model(): string
    {
        return Role::class;
    }
    public function getRoleByName($role) {
        return $this->model->where('name', $role)->first();
    }
}
