<?php

namespace App\Repositories\Interfaces;

interface LikeRepositoryInterface extends RepositoryInterface
{
    public function createLike(
        $post_id,
        $user_id,
        $comment_id
    );
    public function findLike(
        $post_id,
        $user_id,
        $comment_id
    );
    public function totalLike(
        $post_id,
        $comment_id
    );
}
