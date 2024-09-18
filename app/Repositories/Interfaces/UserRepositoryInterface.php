<?php

namespace App\Repositories\Interfaces;

interface UserRepositoryInterface extends RepositoryInterface
{
    public function findLoggedUser();
    public function getUserByName($name);
    public function createUser(
        $username,
        $password
    );
}
