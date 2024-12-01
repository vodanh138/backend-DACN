<?php

namespace App\Repositories;

use App\Models\Like;
use App\Repositories\Interfaces\LikeRepositoryInterface;

class LikeRepository extends BaseRepository implements LikeRepositoryInterface
{
    public function model(): string
    {
        return Like::class;
    }
    public function findLike(
        $post_id,
        $user_id,
        $comment_id
    ) {
        return $this->model
            ->where('post_id', $post_id)
            ->where('user_id', $user_id)
            ->where('comment_id', $comment_id)
            ->first();
    }
    public function createLike(
        $post_id,
        $user_id,
        $comment_id
    ) {
        return $this->model->create(
            [
                'post_id' => $post_id,
                'user_id' => $user_id,
                'comment_id' => $comment_id,
            ]
        );
    }
    public function totalLike(
        $post_id,
        $comment_id
    ) {
        if ($comment_id == null)
            return $this->model
                ->where('post_id', $post_id)
                ->count();
        return $this->model
            ->where('comment_id', $comment_id)
            ->count();
    }
}
