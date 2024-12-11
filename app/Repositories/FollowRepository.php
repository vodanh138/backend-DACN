<?php

namespace App\Repositories;

use App\Models\Follow;
use App\Repositories\Interfaces\FollowRepositoryInterface;

class FollowRepository extends BaseRepository implements FollowRepositoryInterface
{
    public function model(): string
    {
        return Follow::class;
    }
    public function findFollow($user_id, $follow)
    {
        if ($follow == null)
            return $this->model
                ->where('user_id', $user_id)
                ->get();
        return $this->model
            ->where('user_id', $user_id)
            ->where('follow', $follow)
            ->first();
    }
    public function createFollow($user_id, $id)
    {
        return $this->model->create(
            [
                'user_id' => $user_id,
                'follow' => $id,
            ]
        );
    }
    public function totalFollow(
        $id
    ) {
        return $this->model
            ->where('user_id', $id)
            ->count();
    }
    public function totalFollower(
        $id
    ) {
        return $this->model
            ->where('follow', $id)
            ->count();
    }
}
