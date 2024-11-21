<?php

namespace App\Repositories\Interfaces;

interface PostRepositoryInterface extends RepositoryInterface
{
    public function getHomePage($role);
}
