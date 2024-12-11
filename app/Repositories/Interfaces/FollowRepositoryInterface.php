<?php

namespace App\Repositories\Interfaces;

interface FollowRepositoryInterface extends RepositoryInterface
{
    public function createFollow(
        $user_id,
        $id
    );
    public function findFollow(
        $user_id,
        $follow
    );
    public function totalFollow(
        $id
    );
    public function totalFollower(
        $id
    );
}
